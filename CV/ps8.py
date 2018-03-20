"""Problem Set 8: Motion History Images."""

import numpy as np
import cv2

# I/O directories
input_dir = "input"
output_dir = "output"


class MotionHistoryBuilder(object):
    """A motion history image (MHI) builder from sequential video frames."""

    def __init__(self, frame, **kwargs):
        """Initializes motion history builder object.

        Args:
            frame (numpy.array): color BGR uint8 image of initial video frame, values in [0, 255]
            kwargs: additional keyword arguments needed by builder, including:
                    - theta (float): threshold used to compute B_t. Values in [0, 255]
                    - tau (float): value to generate the MHI (M_tau). Default value set to 0.
        """

        self.mhi = np.zeros(frame.shape[:2], dtype=np.float_)  # e.g. motion history image (M_tau)
        self.theta = kwargs.get('theta', 0.)
        self.tau = kwargs.get('tau', 0.)

        # If you want to add more parameters, make sure you set a default value so that
        # your test doesn't fail the autograder because of an unknown or None value.
        #
        # The way to do it is:
        # self.some_parameter_name = kwargs.get('parameter_name', default_value)
        self.old = None

    def get_b_t(self, frame, prev_frame):
        """Calculates the binary image B_t.

        In this method you will implement the equation shown in part 1 of the problem set instructions. You will use
        the class variable self.theta as the threshold value.

        Use numpy operations to speed up this process. You can implement this method without the use of loops.

        Do not perform any morphological operations here, just the equation for B_t(x, y, t).

        Args:
            frame (numpy.array): current frame defined as I_t. Can be int, or float.
            prev_frame (numpy.array): frame from last iteration defined as I_{t-1}. Can be int, or float.

        Returns:
            numpy.array: binary image containing 0s or 1s.

        """
        def threshBinImage(difference,theta):
            output = np.zeros(difference.shape)
            absDiff = np.abs(difference)
            output[absDiff>=theta]=1
            return output

        def getBinary(current,previous,dim_current):
            code = cv2.COLOR_BGR2GRAY
            if len(dim_current)<= 2:
                fr2 = current
                fr1 = previous
            else:
                fr2 = cv2.cvtColor(current,code)
                fr1 = cv2.cvtColor(previous,code)
            diff = (1.0*fr2-1.0*fr1)
            binaryImage = threshBinImage(diff,self.theta)

            return binaryImage

        current = np.copy(frame)
        dim_current = current.shape
        previous = np.copy(prev_frame)

        if prev_frame is not None:
            temp = getBinary(current,prev_frame,dim_current)
            return temp
        else:
            temp =np.zeros((dim_current[0],dim_current[1]),dtype=np.uint8)
            return temp
        pass

    def process(self, frame):
        """Processes a frame of video returning a binary image indicating motion areas.

        This method takes care of computing the B_t(x, y, t) and M_tau(x, y, t) as shown in the problem set
        instructions.

        B_t(x, y, t) notes:
        - Use the function get_b_t to obtain the base binary image.
        - Notice that we are using two frames: I_t and I_{t-1} which represent the current and previous frames
          respectively.
        - Because we don't have a I_{t-1} when working with the first frame, initialize and return the binary image
          creating an array of all zeros.
        - It is recommended to try morphological operations such as erode or dilate to filter the base binary
          image before working on self.mhi (M_tau)
        - This array must only contain 0 and 1 values, not a range between 0 and 1.

        M_tau(x, y, t) notes:
        - It is stored in self.mhi.
        - This array shows the motion progress using a range of values between 0 and tau.
        - When implementing the equation described in the problem set instructions you will notice that motion that
          happened recently shows as brighter areas.
        - Unlike B_t, this image is not a binary array.


        Args:
            frame: frame (numpy.array): color BGR uint8 image of current video frame, values in [0, 255].

        Returns:
            (numpy.array): binary image final B_t (type: bool or uint8), values: 0 (static) or 1 (moving).

        """
        currentMHI = np.copy(self.mhi)
        type = currentMHI.dtype
        tau = self.tau
        k1 = (5,5)
        k2 = (9,9)
        sig = 15
        numruns = 1
        current = np.copy(frame)
        previous = self.old
        if previous is not None:
            for i in range(numruns):
                previous = cv2.GaussianBlur(previous,k1,sigmaX=sig,sigmaY=sig)

        for i in range(numruns):
            current = cv2.GaussianBlur(current,k1,sigmaX=sig,sigmaY=sig)
            current = cv2.GaussianBlur(current,k2,sigmaX=sig,sigmaY=sig)
        self.old = np.copy(current)

        #
        binaryImage = self.get_b_t(current,previous)
        #binaryImage = cv2.morphologyEx(binaryImage, cv2.MORPH_ERODE, np.ones((5,5),np.uint8))

        if np.max(np.max(binaryImage))>0:
            binaryImage = cv2.morphologyEx(binaryImage,cv2.MORPH_CLOSE,np.ones((5,5),np.uint8))
            binaryImage = cv2.morphologyEx(binaryImage,cv2.MORPH_OPEN,np.ones((5,5),np.uint8))
            #binaryImage = cv2.morphologyEx(binaryImage, cv2.MORPH_DILATE, np.ones((5,5),np.uint8))
            #binaryImage = cv2.morphologyEx(binaryImage, cv2.MORPH_ERODE, np.ones((5,5),np.uint8))


        updatedMHI = (currentMHI.astype(np.uint8)-1)
        updatedMHI = updatedMHI.astype(type)
        updatedMHI[binaryImage==1] = tau
        self.mhi = updatedMHI

        return binaryImage
        pass

    def get_mhi(self):
        """Returns the motion history image computed so far.

        Make sure the MHI is updated in process(), perform any final steps here (e.g. normalize to [0, 1])

        Returns:
            (numpy.array): float motion history image, values in [0.0, 1.0]

        """

        # Note: This method may not be called for every frame (typically, only once)
        return self.mhi


class Moments(object):
    """Spatial moments of an image - unscaled and scaled."""

    def __init__(self, image):
        """Initializes and computes the spatial moments on a given image.

        This method initializes the central and scaled moments using the equations shown in the problem set
        instructions.

        OpenCV functions are not allowed.

        Args:
            image (numpy.array): single-channel image, uint8 or float.
        """

        self.central_moments = np.zeros((1, 8))  # array: [mu20, mu11, mu02, mu30, mu21, mu12, mu03, mu22]
        self.scaled_moments = np.zeros((1, 8))  # array: [nu20, nu11, nu02, nu30, nu21, nu12, nu03, nu22]

        # Compute all desired moments here (recommended)
        # Note: Make sure computed moments are in correct order
        def updateMoment(image,initial, imgShape):
            imageTotal = np.sum(np.sum(image))
            temp = [0]*8
            temp2 = [0]*8
            dim1 = imgShape[0]
            dim2 = imgShape[1]
            mesh = np.meshgrid(xrange(dim2),xrange(dim1))
            xratio = np.sum(np.sum(mesh[0]*image))/imageTotal
            yratio = np.sum(np.sum(mesh[1]*image))/imageTotal

            xDiff =  mesh[0]-xratio
            xDiffSquared =xDiff**2
            xDiffCubed = xDiff**3

            yDiff = mesh[1]-yratio
            yDiffSquared = yDiff**2
            yDiffCubed = yDiff**3
            temp[0] = np.sum(np.sum( ( xDiffSquared*image )))
            temp2[0] = temp[0]/(imageTotal**2)

            temp[1] = np.sum(np.sum( ((xDiff)*(yDiff))*image ))
            temp2[1] = temp[1]/(imageTotal**2)

            temp[2] = np.sum(np.sum( (yDiffSquared)*image ))
            temp2[2] = temp[2]/(imageTotal**2)

            temp[3] = np.sum(np.sum( (xDiffCubed)*image ))
            temp2[3] = temp[3]/(imageTotal**2.5)

            temp[4] = np.sum(np.sum( ( xDiffSquared*yDiff )*image ))
            temp2[4] = temp[4]/(imageTotal**2.5)

            temp[5] = np.sum(np.sum( (  xDiff*yDiffSquared   )*image ))
            temp2[5] = temp[5]/(imageTotal**2.5)

            temp[6] = np.sum(np.sum( (yDiffCubed)*image ))
            temp2[6] = temp[6]/(imageTotal**2.5)

            temp[7] = np.sum(np.sum( (xDiffSquared*yDiffSquared)*image ))
            temp2[7] = temp[7]/(imageTotal**3)


            return np.asarray(temp).reshape((1,8)),np.asarray(temp2).reshape((1,8))


            pass

        imageTotal = np.sum(np.sum(image))

        self.central_moments,self.scaled_moments = updateMoment(image,np.copy(self.central_moments),image.shape)


    def get_central_moments(self):
        """Returns the central moments as NumPy array.

        These are to be built in the __init__ function.

        Order: [mu20, mu11, mu02, mu30, mu21, mu12, mu03, mu22].

        Returns:
            (numpy.array): float array of central moments.

        """

        return self.central_moments

    def get_scaled_moments(self):
        """Returns scaled central moments as NumPy array.

        These are to be built in the __init__ function.

        Order: [nu20, nu11, nu02, nu30, nu21, nu12, nu03, nu22].

        Returns:
            (numpy.array): float array of scaled central moments.

        """

        return self.scaled_moments


def compute_feature_difference(a_features, b_features, scale=0.5):
    """Computes feature difference between two videos.

    This function is called by the method match_features located in experiment.py. The features used are the dictionary
    items.

    The feature difference can be seen as the euclidean distance between the input features. If you decide to use the
    scale parameter, this distance uses a weighted difference of the input features when calculating the L2 norm:

        scale * a - (1 - scale) * b


    Args:
        a_features: features from one video, MHI & MEI moments in a 16-element 1D array.
        b_features: like a_features, from other video.
        scale (float): scale factor for compute_feature_difference (if needed).

    Returns:
        diff (float): a single value, difference between the two feature vectors.

    """

    # Tip: Scale/weight difference values to get better results as moment magnitudes differ
    scale1 = scale
    scale2 = 1-scale
    diff = scale1*a_features-scale2*b_features
    output = np.linalg.norm(diff)
    return  output
    pass
