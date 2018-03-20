import cv2
import numpy as np


def disparity_ssd(img1, img2, direction, w_size, dmax):
    def prep_model_based_on_direction(img1,img2,direction):
        return (np.copy(img1),np.copy(img2),-1) if direction else (np.copy(img2),np.copy(img1),1)
    """Returns a disparity map D(y, x) using the Sum of Squared Differences.

    Assuming img1 and img2 are the left (L) and right (R) images from the same scene. The disparity image contains
    values such that: L(y, x) = R(y, x) + D(y, x) when matching from left (L) to right (R).

    This method uses the Sum of Squared Differences as an error metric. Refer to:
    https://software.intel.com/en-us/node/504333

    The algorithm used in this method follows the pseudocode:

    height: number of rows in img1 or img2.
    width: number of columns in img1 or img2.
    DSI: initial array containing only zeros of shape (height, width, dmax)
    kernel: array of shape (w_size[0], w_size[1]) where each value equals to 1/(w_size[0] * w_size[1]). This allows
            a uniform distribution that sums to 1.

    for d going from 0 to dmax:
        shift = some_image_shift_function(img2, d)
        diff = img1 - shift  # SSD
        Square every element values  # SSD
        Run a 2D correlation filter (i.e. cv.filter2D) using the kernel defined above
        Save the results in DSI(:, :, d)

    For each location r, c the SSD for an offset d is in DSI(r,c,d). The best match for pixel r,c is represented by
    the index d for which DSI(r,c,d) is smallest.

    Args:
        img1 (numpy.array): grayscale image, in range [0.0, 1.0].
        img2 (numpy.array): grayscale image, in range [0.0, 1.0] same shape as img1.
        direction (int): if 1: match right to left (shift img1 left).
                         if 0: match left to right (shift img2 right).
        w_size (tuple): window size, type int representing both height and width (h, w).
        dmax (int): maximum value of pixel disparity to test.

    Returns:
        numpy.array: Disparity map of type int64, 2-D array of the same shape as img1 or img2.
                     This array contains the d values representing how far a certain pixel has been displaced.
                     Return without normalizing or clipping.
    """
    img1,img2,dr = prep_model_based_on_direction(img1,img2,direction)
    img_shape = img1.shape
    y_len = int(img_shape[0])
    x_len = int(img_shape[1])
    ddepth =-1
    disparity_map = (np.ones((y_len,x_len,dmax))*150)

    #kernel: array of shape (w_size[0], w_size[1]) where each value equals to 1/(w_size[0] * w_size[1]). This allows
    #        a uniform distribution that sums to 1.
    kernel =np.ones(w_size)/(w_size[0]*w_size[1])

    #Similar to PS1 in shiftImageLeft
    img1_padded = cv2.copyMakeBorder(img1,int(np.floor(w_size[0]/2)),int(np.floor(w_size[0]/2)),int(np.floor(w_size[1]/2)),int(np.floor(w_size[1]/2)),cv2.BORDER_REPLICATE)
    img2_padded = cv2.copyMakeBorder(img2,int(np.floor(w_size[0]/2)),int(np.floor(w_size[0]/2)),int(np.floor(w_size[1]/2)),int(np.floor(w_size[1]/2)),cv2.BORDER_REPLICATE)

    for d in range(0,dmax):
        shiftBy = d*dr
        shift = np.roll(img1_padded, shiftBy)
        diff = img2_padded-shift
        #Square every element values  # SSD
        diff = diff**2
        #Run a 2D correlation filter (i.e. cv.filter2D) using the kernel defined above
        disparity_map[:,:,d] =cv2.filter2D(diff,ddepth,kernel)[int(np.floor(w_size[0]/2)):int(np.floor(w_size[0]/2))+y_len,int(np.floor(w_size[1]/2)):int(np.floor(w_size[1]/2))+x_len]

    return np.argmin(disparity_map,axis=2)
    pass


def disparity_ncorr(img1, img2, direction, w_size, dmax):
    def prep_model_based_on_direction(img1,img2,direction):
        return (np.copy(img1),np.copy(img2),-1) if direction else (np.copy(img2),np.copy(img1),1)
    """Returns a disparity map D(y, x) using the normalized correlation method.

    This method uses a similar approach used in disparity_ssd replacing SDD with the normalized correlation metric.

    For more information refer to:
    https://software.intel.com/en-us/node/504333

    Unlike SSD, the best match for pixel r,c is represented by the index d for which DSI(r,c,d) is highest.

    Args:
        img1 (numpy.array): grayscale image, in range [0.0, 1.0].
        img2 (numpy.array): grayscale image, in range [0.0, 1.0] same shape as img1.
        direction (int): if 1: match right to left (shift img1 left).
                         if 0: match left to right (shift img2 right).
        w_size (tuple): window size, type int representing both height and width (h, w).
        dmax (int): maximum value of pixel disparity to test.

    Returns:
        numpy.array: Disparity map of type int64, 2-D array of the same shape size as img1 or img2.
                     This array contains the d values representing how far a certain pixel has been displaced.
                     Return without normalizing or clipping.
    """
    img1,img2,dr = prep_model_based_on_direction(img1,img2,direction)
    img_shape = img1.shape
    y_len = int(img_shape[0])
    x_len = int(img_shape[1])
    ddepth =-1
    disparity_map = np.zeros((y_len,x_len,dmax))

    #kernel: array of shape (w_size[0], w_size[1]) where each value equals to 1/(w_size[0] * w_size[1]). This allows
    #        a uniform distribution that sums to 1.
    kernel =np.ones(w_size)/(w_size[0]*w_size[1])

    #Similar to PS1 in shiftImageLeft
    img1_padded = cv2.copyMakeBorder(img1,int(np.floor(w_size[0]/2)),int(np.floor(w_size[0]/2)),int(np.floor(w_size[1]/2)),int(np.floor(w_size[1]/2)),cv2.BORDER_REPLICATE)
    img2_padded = cv2.copyMakeBorder(img2,int(np.floor(w_size[0]/2)),int(np.floor(w_size[0]/2)),int(np.floor(w_size[1]/2)),int(np.floor(w_size[1]/2)),cv2.BORDER_REPLICATE)

    for d in range(0,dmax):
        shiftBy = d*dr
        shift = np.roll(img1_padded, shiftBy)
        my_diff = ((img2_padded*shift))
        kernel_output = cv2.filter2D(my_diff,-1,kernel)
        divisor = np.sqrt(cv2.filter2D(img2_padded**2,-1,kernel*(w_size[0]*w_size[1]))*cv2.filter2D(shift**2,-1,kernel*(w_size[0]*w_size[1])))
        new_kernel = kernel_output/divisor
        disparity_map[:,:,d] = new_kernel[int(np.floor(w_size[0]/2)):int(np.floor(w_size[0]/2))+y_len,int(np.floor(w_size[1]/2)):int(np.floor(w_size[1]/2))+x_len]

    return np.argmax(disparity_map,axis=2)

    pass


def add_noise(img, sigma):
    """Returns a copy of the input image with gaussian noise added. The Gaussian noise mean must be zero.
    The parameter sigma controls the standard deviation of the noise.

    Args:
        img (numpy.array): input image of type int or float.
        sigma (float): gaussian noise standard deviation.

    Returns:
        numpy.array: output image with added noise of type float64. Return it without normalizing or clipping it.
    """
    output = np.copy(img).astype(float) + np.random.randn(img.shape[0],img.shape[1])*sigma
    return output
    pass


def increase_contrast(img, percent):
    """Returns a copy of the input image with an added contrast by a percentage factor.

    Args:
        img (numpy.array): input image of type int or float.
        percent (int or float): value to increase contrast. The autograder uses percentage values i.e. 10%.

    Returns:
        numpy.array: output image with added noise of type float64. Return it without normalizing or clipping it.
    """
    return np.copy(img).astype(float)*(1.0+percent/100.0)
    pass
