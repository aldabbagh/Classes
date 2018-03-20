"""Problem Set 5: Harris, ORB, RANSAC."""

import numpy as np
import cv2


def gradient_x(image):
    """Computes the image gradient in X direction.

    This method returns an image gradient considering the X direction. See cv2.Sobel.

    Args:
        image (numpy.array): grayscale floating-point image with values in [0.0, 1.0].

    Returns:
        numpy.array: image gradient in X direction with values in [-1.0, 1.0].
    """

    fltr = np.array([[-1.0,0.0,1.0],
                     [-2.0,0.0,2.0],
                     [-1.0,0.0,1.0]])

    fltr = fltr/8.0

    gradient_X = cv2.filter2D(image, -1, fltr, borderType=cv2.BORDER_DEFAULT)
    return gradient_X
    pass


def gradient_y(image):
    """Computes the image gradient in Y direction.

    This method returns an image gradient considering the Y direction. See cv2.Sobel.

    Args:
        image (numpy.array): grayscale floating-point image with values in [0.0, 1.0].

    Returns:
        numpy.array: image gradient in Y direction with values in [-1.0, 1.0].
    """

    fltr = np.array([[1.0,2.0,1.0],
                     [0.0,0.0,0.0],
                     [-1.0,-2.0,-1.0]])

    fltr = fltr/8.0

    gradient_Y = cv2.filter2D(image, -1, fltr, borderType=cv2.BORDER_DEFAULT)

    return gradient_Y
    pass


def make_image_pair(image1, image2):
    """Adjoins two images side-by-side to make a single new image.

    The output dimensions must take the maximum height from both images for the total height.
    The total width is found by adding the widths of image1 and image2.

    Args:
        image1 (numpy.array): first image, could be grayscale or color (BGR).
                              This array takes the left side of the output image.
        image2 (numpy.array): second image, could be grayscale or color (BGR).
                              This array takes the right side of the output image.

    Returns:
        numpy.array: combination of both images, side-by-side, same type as the input size.
    """
    image1 = image1.astype(np.float64)
    image2 = image2.astype(np.float64)

    shape1_A = image1.shape[0]

    shape2_A = image1.shape[1]
    shape2_B = image2.shape[1]
    if len(image1.shape)==2:
        newImage = np.zeros((shape1_A,shape2_A+shape2_B))
        newImage[:,0:shape2_A] = image1
        newImage[:,shape2_A:] = image2
    else:
        newImage = np.zeros((shape1_A,shape2_A+shape2_B,3))
        newImage[:,0:shape2_A,:] = image1
        newImage[:,shape2_A:,:] = image2

    #return newImage.astype(np.int64)
    return newImage

    pass


def harris_response(ix, iy, kernel_dims, alpha):
    """Computes the Harris response map using given image gradients.

    Args:
        ix (numpy.array): image gradient in the X direction with values in [-1.0, 1.0].
        iy (numpy.array): image gradient in the Y direction with the same shape and type as Ix.
        kernel_dims (tuple): 2D windowing kernel dimensions. ie. (3, 3)  (3, 5).
        alpha (float): Harris detector parameter multiplied with the square of trace.

    Returns:
        numpy.array: Harris response map, same size as inputs, floating-point.
    """
    krnl_dim = kernel_dims
    ddepth = -1
    inputs = [ix*ix, ix*iy, iy*ix, iy*iy]
    results = []

    for i in range(len(inputs)):
        array = inputs[i]
        y_len = array.shape[0]
        x_len = array.shape[1]
        kernel =np.ones(krnl_dim)/(krnl_dim[0]*krnl_dim[1])
        array_padded = cv2.copyMakeBorder(array,int(np.floor(krnl_dim[0]/2)),int(np.floor(krnl_dim[0]/2)),int(np.floor(krnl_dim[1]/2)),int(np.floor(krnl_dim[1]/2)),cv2.BORDER_REPLICATE)
        output = cv2.filter2D(array_padded,-1,kernel)[int(np.floor(krnl_dim[0]/2)):int(np.floor(krnl_dim[0]/2))+y_len, int(np.floor(krnl_dim[1]/2)):int(np.floor(krnl_dim[1]/2))+x_len]
        results.append(output)

    A = (results[0]*results[3]-results[1]*results[2])
    B = results[0]+results[3]
    harrisResponse = A-alpha*B*B

    return harrisResponse

    pass


def find_corners(r_map, threshold, radius):
    """Finds corners in a given response map.

    This method uses a circular region to define the non-maxima suppression area. For example,
    let c1 be a corner representing a peak in the Harris response map, any corners in the area
    determined by the circle of radius 'radius' centered in c1 should not be returned in the
    peaks array.

    Make sure you account for duplicate and overlapping points.

    Args:
        r_map (numpy.array): floating-point response map, e.g. output from the Harris detector.
        threshold (float): value between 0.0 and 1.0. Response values less than this should
                           not be considered plausible corners.
        radius (int): radius of circular region for non-maximal suppression.

    Returns:
        numpy.array: peaks found in response map R, each row must be defined as [x, y]. Array
                     size must be N x 2, where N are the number of points found.
    """

    # Normalize R
    r_map_norm = cv2.normalize(r_map, alpha=0, beta=1, norm_type=cv2.NORM_MINMAX)
    # Do not modify the code above. Continue working with r_map_norm.

    Output =[]
    H_norm = np.copy(r_map_norm)
    H_norm [H_norm < threshold] = 0
    r_nhood = int(H_norm.shape[0])
    c_nhood = int(H_norm.shape[1])

    finished = False
    while not finished:
        point = np.unravel_index(H_norm.argmax(), H_norm.shape)
        row_id = int(point[0])
        col_id = int(point[1])

        if row_id>radius:
            if r_nhood>row_id+radius:
                r_vals = range(row_id-radius,row_id+radius)
            else:
                r_vals = range(row_id-radius,r_nhood)
        else:
            if r_nhood>row_id+radius:
                r_vals = range(0,row_id+radius)
            else:
                r_vals = range(0,r_nhood)

        if col_id>radius:
            if c_nhood>col_id+radius:
                c_vals = range(col_id-radius,col_id+radius)
            else:
                c_vals = range(col_id-radius,c_nhood)
        else:
            if c_nhood>col_id+radius:
                c_vals = range(0,col_id+radius)
            else:
                c_vals = range(0,c_nhood)


        Output.append([col_id,row_id])

        #Zero the neighboorhood Area
        for c in c_vals:
            for r in r_vals:
                distance = (c-col_id)**2+(r-row_id)**2
                if radius*radius>= distance:
                    H_norm[r,c] = 0

        if np.amax(H_norm)==0:
            finished = True

    return np.asarray(Output)

    pass


def draw_corners(image, corners):
    """Draws corners on (a copy of) the given image.

    Args:
        image (numpy.array): grayscale floating-point image, values in [0.0, 1.0].
        corners (numpy.array): peaks found in response map R, as a sequence of [x, y] coordinates.
                               Array size must be N x 2, where N are the number of points found.

    Returns:
        numpy.array: copy of the input image with corners drawn on it, in color (BGR).
    """
    img_copy = np.copy(image).astype(np.float32)

    # copy of the input image with corners drawn on it, in color (BGR).
    # need to convert copy to colored image
    img_copy = cv2.cvtColor(img_copy,cv2.COLOR_GRAY2BGR)
    for i in range(len(corners)):
        y = corners[i][1]
        x = corners[i][0]
        cv2.circle(img_copy,(x,y),2,(0, 0, 1))
    return img_copy
    pass


def gradient_angle(ix, iy):
    """Computes the angle (orientation) image given the X and Y gradients.

    Args:
        ix (numpy.array): image gradient in X direction.
        iy (numpy.array): image gradient in Y direction, same size and type as Ix

    Returns:
        numpy.array: gradient angle image, same shape as ix and iy. Values must be in degrees [0.0, 360).
    """
    import math

    def getAngle(x,y):
        radianAngle = np.arctan2(y,x)
        degreeAngle = math.degrees(radianAngle)
        return degreeAngle

    dim1 = ix.shape[0]
    dim2 = ix.shape[1]

    gradient_angle_image = np.zeros((dim1,dim2))

    for row in range(dim1):
        for column in range(dim2):
            x = ix[row,column]
            y = iy[row,column]
            temp = getAngle(x,y)
            gradient_angle_image[row,column] = temp

    return gradient_angle_image
    pass


def get_keypoints(points, angle, size, octave=0):
    """Creates OpenCV KeyPoint objects given interest points, response map, and angle images.

    See cv2.KeyPoint and cv2.drawKeypoint.

    Args:
        points (numpy.array): interest points (e.g. corners), array of [x, y] coordinates.
        angle (numpy.array): gradient angle (orientation) image, each value in degrees [0, 360).
                             Keep in mind this is a [row, col] array. To obtain the correct
                             angle value you should use angle[y, x].
        size (float): fixed _size parameter to pass to cv2.KeyPoint() for all points.
        octave (int): fixed _octave parameter to pass to cv2.KeyPoint() for all points.
                      This parameter can be left as 0.

    Returns:
        keypoints (list): a sequence of cv2.KeyPoint objects
    """

    # Note: You should be able to plot the keypoints using cv2.drawKeypoints() in OpenCV 2.4.9+
    output=[]
    numpoints = len(points)
    for point in points:
        output.append(cv2.KeyPoint(x=point[0],y=point[1],_size=size,_angle=angle[point[1],point[0]],_octave=octave))
    return output
    pass


def get_descriptors(image, keypoints):
    """Extracts feature descriptors from the image at each keypoint.

    This function finds descriptors following the methods used in cv2.ORB. You are allowed to
    use such function or write your own.

    Args:
        image (numpy.array): input image where the descriptors will be computed from.
        keypoints (list): a sequence of cv2.KeyPoint objects.

    Returns:
        tuple: 2-element tuple containing:
            descriptors (numpy.array): 2D array of shape (len(keypoints), 32).
            new_kp (list): keypoints from ORB.compute().
    """

    # Normalize image
    image_norm = cv2.normalize(image, alpha=0, beta=255, norm_type=cv2.NORM_MINMAX, dtype=cv2.CV_8U)
    # Do not modify the code above. Continue working with r_norm.

    # Note: You can use OpenCV's ORB.compute() method to extract descriptors, or write your own!
    new_kp, descriptors = cv2.ORB().compute(np.copy(image_norm),keypoints)

    return (descriptors,new_kp)

    pass


def match_descriptors(desc1, desc2):
    """Matches feature descriptors obtained from two images.

    Use cv2.NORM_HAMMING and cross check for cv2.BFMatcher. Return the matches sorted by distance.

    Args:
        desc1 (numpy.array): descriptors from image 1, as returned by ORB.compute().
        desc2 (numpy.array): descriptors from image 2, same format as desc1.

    Returns:
        list: a sequence (list) of cv2.DMatch objects containing corresponding descriptor indices.
    """

    # Note: You can use OpenCV's descriptor matchers, or write your own!
    #       Make sure you use Hamming Normalization to match the autograder.
    return cv2.BFMatcher(cv2.NORM_HAMMING,crossCheck=True).match(desc1, desc2)
    pass


def draw_matches(image1, image2, kp1, kp2, matches):
    """Shows matches by drawing lines connecting corresponding keypoints.

    Results must be presented joining the input images side by side (use make_image_pair()).

    OpenCV's match drawing function(s) are not allowed.

    Args:
        image1 (numpy.array): first image
        image2 (numpy.array): second image, same type as first
        kp1 (list): list of keypoints (cv2.KeyPoint objects) found in image1
        kp2 (list): list of keypoints (cv2.KeyPoint objects) found in image2
        matches (list): list of matching keypoint index pairs (as cv2.DMatch objects)

    Returns:
        numpy.array: image1 and image2 joined side-by-side with matching lines;
                     color image (BGR), uint8, values in [0, 255].
    """

    # Note: DO NOT use OpenCV's match drawing function(s)! Write your own.
    def toColor(img):
        img =  img.astype(np.float32)
        colored = cv2.cvtColor(img, cv2.COLOR_GRAY2BGR)
        return colored

    normalized = cv2.normalize(make_image_pair(image1,image2), alpha=0, beta=1, norm_type=cv2.NORM_MINMAX)
    colored = toColor(normalized)

    dim1,dim2 = image2.shape

    for i in range(len(matches)):
        potential = matches[i]
        p1 = kp1[potential.queryIdx].pt
        p2 = kp2[potential.trainIdx].pt

        p1x = int(p1[0])
        p1y = int(p1[1])
        p2x = int(p2[0] + dim2)
        p2y = int(p2[1])

        point1 = (p1x,p1y)
        point2 = (p2x,p2y)

        cv2.line(colored, point1, point2, (0, 0, 1), 1)

    return colored
    pass


def compute_translation_RANSAC(kp1, kp2, matches, thresh):
    """Computes the best translation vector using RANSAC given keypoint matches.

    Args:
        kp1 (list): list of keypoints (cv2.KeyPoint objects) found in image1.
        kp2 (list): list of keypoints (cv2.KeyPoint objects) found in image2.
        matches (list): list of matches (as cv2.DMatch objects).
        thresh (float): offset tolerance in pixels which decides if a match forms part of
                        the consensus. This value can be seen as a minimum delta allowed
                        between point components.

    Returns:
        tuple: 2-element tuple containing:
            translation (numpy.array): translation/offset vector <x, y>, array of shape (2, 1).
            good_matches (list): consensus set of matches that agree with this translation.
    """

    # Note: this function must use the RANSAC method. If you implement any non-RANSAC approach
    # (i.e. brute-force) you will not get credit for either the autograder tests or the report
    # sections that depend of this function.
    from random import shuffle
    def N_formula():
        N = np.log10(0.01)/np.log10(0.9)
        N = np.ceil(N)
        return int(N)

    def comparetoThresh(x1,y1,x2,y2,thresh):
        diff1 = np.abs(y2-y1)<=thresh
        diff2 = np.abs(x2-x1)< thresh
        return diff1 and diff2

    def helperFunction(match,kp1,kp2):
        p1 = kp1[match.queryIdx].pt
        p2 = kp2[match.trainIdx].pt
        p1x = p1[0]
        p1y = p1[1]
        p2x = p2[0]
        p2y = p2[1]
        return (p2x-p1x  ,p2y-p1y)

    def updateModel(current_model,translation,translation_size,myset):
        translation_size = len(myset)
        translation = np.mean(current_model,axis=0)
        return translation,translation_size


    total_matches = len(matches)
    match_indices = range(total_matches)
    good_matches = []
    translation =[]
    translation_size = 0

    N_trials = range(N_formula())
    for trial in N_trials:
        shuffle(matches)
        numMatches = 1
        chosenMatch = matches[:numMatches]
        myset = [chosenMatch]

        # This should change between different ransac functions
        deltas = helperFunction(chosenMatch[0],kp1,kp2)
        mydx = deltas[0]
        mydy = deltas[1]
        current_model = np.array([[mydx,mydy]])
        for i in range(len(matches[numMatches:])):
            currentMatch = matches[numMatches:][i]
            deltas = helperFunction(currentMatch,kp1,kp2)
            current_dx = deltas[0]
            current_dy = deltas[1]

            lessThanThresh = comparetoThresh(current_dx,current_dy,mydx,mydy,thresh)
            if lessThanThresh:
                myset.append([currentMatch])
                current_model =np.append(current_model, [[current_dx,current_dy]], axis=0)

            if translation_size<len(myset):
                translation,translation_size = updateModel(current_model,translation,translation_size,myset)

        goodmatchsize = len(good_matches)
        mysetsize = len(myset)
        if goodmatchsize < mysetsize:
            good_matches = myset

        goodmatchpoints = []
        for goodmatch in good_matches:
            goodmatchpoints.append([kp1[goodmatch[0].queryIdx], kp2[goodmatch[0].trainIdx]])

    print "Percentage of 1: ", float(len(goodmatchpoints))/float(len(matches))
    return translation.reshape((-1,1)),goodmatchpoints


    pass


def compute_similarity_RANSAC(kp1, kp2, matches, thresh):
    """Computes the best similarity transform using RANSAC given keypoint matches.

    Args:
        kp1 (list): list of keypoints (cv2.KeyPoint objects) found in image1.
        kp2 (list): list of keypoints (cv2.KeyPoint objects) found in image2.
        matches (list): list of matches (as cv2.DMatch objects).
        thresh (float): offset tolerance in pixels which decides if a match forms part of
                        the consensus. This value can be seen as a minimum delta allowed
                        between point components.

    Returns:
        tuple: 2-element tuple containing:
            m (numpy.array): similarity transform matrix of shape (2, 3).
            good_matches (list): consensus set of matches that agree with this transformation.
    """

    # Note: this function must use the RANSAC method. If you implement any non-RANSAC approach
    # (i.e. brute-force) you will not get credit for either the autograder tests or the report
    # sections that depend of this function.

    from random import shuffle
    def N_formula():
        N = np.log10(0.01)/np.log10(1-0.1**2)
        N = np.ceil(N)
        return int(N)

    def comparetoThresh(model1,model2,thresh):
        diff = np.absolute(model1-model2)
        diff1 = np.amax(diff)<thresh
        return diff1

    def findSolution(A,B):
        sol = np.linalg.lstsq(A,B)
        sol = sol[0]
        sol = np.array([sol[0],-1*sol[1],sol[2],sol[1],sol[0],sol[3]]).T
        return sol

    def helperFunction(chosenmatch1,chosenmatch2,kp1,kp2):
        p1 = (chosenmatch1.queryIdx,chosenmatch1.trainIdx)
        p2 = (chosenmatch2.queryIdx,chosenmatch2.trainIdx)
        p1x = kp1[p1[0]].pt
        p1y = kp2[p1[1]].pt
        p2x = kp1[p2[0]].pt
        p2y = kp2[p2[1]].pt

        u1 = p1x[0]
        v1 = p1x[1]
        u1p = p1y[0]
        v1p = p1y[1]

        u2 = p2x[0]
        v2 = p2x[1]
        u2p = p2y[0]
        v2p = p2y[1]

        A = np.array([[u1,-1.0*v1,1,0],[v1,u1,0,1],[u2,-1.0*v2,1,0],[v2,u2,0,1]])
        B = np.array([[u1p],[v1p],[u2p],[v2p]])
        return findSolution(A,B)

    def updateModel(current_model,translation,translation_size,myset):
        translation_size = len(myset)
        translation = np.mean(current_model,axis=0)
        return translation,translation_size


    total_matches = len(matches)
    match_indices = range(total_matches)
    good_matches = []
    translation =[]
    translation_size = 0

    N_trials = range(N_formula())
    for trial in N_trials:
        shuffle(matches)
        numMatches = 2
        chosenMatch = matches[:numMatches]
        myset = [chosenMatch[0],chosenMatch[1]]

        my_model = helperFunction(myset[0],myset[1],kp1,kp2)
        # This should change between different ransac functions
        current_model = my_model
        for i in range(0,len(matches[numMatches:])/2,2):
            match1 = matches[numMatches:][i]
            match2 = matches[numMatches:][i+1]
            new_model = helperFunction(match1,match2,kp1,kp2)

            lessThanThresh = comparetoThresh(my_model,new_model,thresh)
            if lessThanThresh:
                myset.append(match1)
                myset.append(match2)
                current_model =np.append(current_model, new_model, axis=0)

            if translation_size<len(myset):
                translation,translation_size = updateModel(current_model,translation,translation_size,myset)

        goodmatchsize = len(good_matches)
        mysetsize = len(myset)
        if goodmatchsize < mysetsize:
            good_matches = myset

        goodmatchpoints = []
        for goodmatch in good_matches:
            goodmatchpoints.append([kp1[goodmatch.queryIdx], kp2[goodmatch.trainIdx]])

    print "Percentage of 2: ", float(len(goodmatchpoints))/float(len(matches))
    return translation.reshape((-1,1)).reshape((2,3)),goodmatchpoints

    pass


def compute_affine_RANSAC(kp1, kp2, matches, thresh):
    """ Compute the best affine transform using RANSAC given keypoint matches.

    Args:
        kp1 (list): list of keypoints (cv2.KeyPoint objects) found in image1
        kp2 (list): list of keypoints (cv2.KeyPoint objects) found in image2
        matches (list): list of matches (as cv2.DMatch objects)
        thresh (float): offset tolerance in pixels which decides if a match forms part of
                        the consensus. This value can be seen as a minimum delta allowed
                        between point components.

    Returns:
        tuple: 2-element tuple containing:
            m (numpy.array): affine transform matrix of shape (2, 3)
            good_matches (list): consensus set of matches that agree with this transformation.
    """

    # Note: this function must use the RANSAC method. If you implement any non-RANSAC approach
    # (i.e. brute-force) you will not get credit for either the autograder tests or the report
    # sections that depend of this function.

    from random import shuffle
    def N_formula():
        N = np.log10(0.01)/np.log10(1-0.1**3)
        N = np.ceil(N)
        return int(N)

    def comparetoThresh(model1,model2,thresh):
        diff = np.absolute(model1-model2)
        diff1 = np.amax(diff)<thresh
        return diff1

    def findSolution(A,B):
        sol = np.linalg.lstsq(A,B)
        sol = sol[0]
        sol = np.array([sol[0],sol[1],sol[2],sol[3],sol[4],sol[5]]).T
        return sol

    def helperFunction(chosenmatch1,chosenmatch2,chosenmatch3,kp1,kp2):
        p1 = (chosenmatch1.queryIdx,chosenmatch1.trainIdx)
        p2 = (chosenmatch2.queryIdx,chosenmatch2.trainIdx)
        p3 = (chosenmatch3.queryIdx,chosenmatch3.trainIdx)
        p1x = kp1[p1[0]].pt
        p1y = kp2[p1[1]].pt
        p2x = kp1[p2[0]].pt
        p2y = kp2[p2[1]].pt
        p3x = kp1[p3[0]].pt
        p3y = kp2[p3[1]].pt

        u1 = p1x[0]
        v1 = p1x[1]
        u1p = p1y[0]
        v1p = p1y[1]

        u2 = p2x[0]
        v2 = p2x[1]
        u2p = p2y[0]
        v2p = p2y[1]

        u3 = p3x[0]
        v3 = p3x[1]
        u3p = p3y[0]
        v3p = p3y[1]

        A = np.array([[u1,v1,1,0,0,0],[0,0,0,u1,v1,1],[u2, v2, 1, 0, 0, 0],[0, 0, 0, u2, v2, 1],[u3, v3, 1, 0, 0, 0],[0, 0, 0, u3, v3, 1]])
        B = np.array([[u1p],[v1p],[u2p],[v2p],[u3p],[v3p]])
        return findSolution(A,B)

    def updateModel(current_model,translation,translation_size,myset):
        translation_size = len(myset)
        translation = np.mean(current_model,axis=0)
        return translation,translation_size


    total_matches = len(matches)
    match_indices = range(total_matches)
    good_matches = []
    translation =[]
    translation_size = 0

    N_trials = range(N_formula())
    for trial in N_trials:
        shuffle(matches)
        numMatches = 3
        chosenMatch = matches[:numMatches]
        myset = [chosenMatch[0],chosenMatch[1],chosenMatch[2]]

        my_model = helperFunction(myset[0],myset[1],myset[2],kp1,kp2)
        # This should change between different ransac functions
        current_model = my_model
        for i in range(0,len(matches[numMatches:])/3,3):
            match1 = matches[numMatches:][i]
            match2 = matches[numMatches:][i+1]
            match3 = matches[numMatches:][i+2]
            new_model = helperFunction(match1,match2,match3,kp1,kp2)

            lessThanThresh = comparetoThresh(my_model,new_model,thresh)
            if lessThanThresh:
                myset.append(match1)
                myset.append(match2)
                current_model =np.append(current_model, new_model, axis=0)

            if translation_size<len(myset):
                translation,translation_size = updateModel(current_model,translation,translation_size,myset)

        goodmatchsize = len(good_matches)
        mysetsize = len(myset)
        if goodmatchsize < mysetsize:
            good_matches = myset

        goodmatchpoints = []
        for goodmatch in good_matches:
            goodmatchpoints.append([kp1[goodmatch.queryIdx], kp2[goodmatch.trainIdx]])

    print "Percentage of: ", float(len(goodmatchpoints))/float(len(matches))
    return translation.reshape((-1,1)).reshape((2,3)),goodmatchpoints

    pass


def warp_img(img_a, img_b, m):
    """Warps image B using a transformation matrix.

    Keep in mind:
    - Write your own warping function. No OpenCV functions are allowed.
    - If you see several black pixels (dots) in your image, it means you are not
      implementing backwards warping.
    - If line segments do not seem straight you can apply interpolation methods.
      https://en.wikipedia.org/wiki/Interpolation
      https://en.wikipedia.org/wiki/Bilinear_interpolation

    Args:
        img_a (numpy.array): reference image.
        img_b (numpy.array): image to be warped.
        m (numpy.array): transformation matrix, array of shape (2, 3).

    Returns:
        tuple: 2-element tuple containing:
            warpedB (numpy.array): warped image.
            overlay (numpy.array): reference and warped image overlaid. Copy the reference
                                   image in the red channel and the warped image in the
                                   green channel
    """

    # Note: Write your own warping function. No OpenCV warping functions are allowed.
    def withinBoundary(u_prime,v_prime,shape):
        condition1 = u_prime<=shape[0]-1.0 and u_prime>=0
        condition2 = v_prime<=shape[1]-1.0 and v_prime>=0
        return condition1 and condition2

    def findProportions(u_prime,v_prime):
        out = np.zeros((4,2))

        out[0,:] = [np.floor(u_prime),np.ceil(u_prime)-u_prime]
        out[1,:] = [np.floor(u_prime),1-out[0,1]]
        out[2,:] = [np.floor(v_prime),np.ceil(v_prime)-v_prime]
        out[3,:] = [np.floor(v_prime),1-out[2,1]]

        return out

    def getNewValue(mapping,img):
        newValue = 0
        u1 = int(mapping[0,0])
        v1 = int(mapping[2,0])
        u2 = int(mapping[1,0])
        v2 = int(mapping[3,0])

        if u1<img.shape[0]:
            if v1<img.shape[1]:
                pxl = img[u1,v1]
                newValue += pxl*mapping[0,1]*mapping[2,1]
            if v2<img.shape[1]:
                pxl = img[u1,v2]
                newValue += pxl*mapping[0,1]*mapping[3,1]
        if u2<img.shape[0]:
            if v1<img.shape[1]:
                pxl = img[u2,v1]
                newValue += pxl*mapping[1,1]*mapping[2,1]
            if v2<img.shape[1]:
                pxl = img[u2,v2]
                newValue += pxl*mapping[1,1]*mapping[3,1]
        return newValue

    m_copy = np.copy(m)
    m_copy[0,1] = -1.0*m_copy[0,1]
    m_copy[0,2] = -1.0*m_copy[0,2]
    m_copy[1,2] = -1.0*m_copy[1,2]

    warpedB = np.zeros(img_a.shape)
    overlay = np.zeros(img_a.shape+(3,))

    for row in range(img_a.shape[0]):
        for column in range(img_a.shape[1]):
            u = row
            v = column
            p1 = [[u],
                  [v],
                  [1]]
            (u_prime,v_prime) = np.dot(m_copy,np.asarray(p1))
            if withinBoundary(u_prime,v_prime,img_b.shape):
                mapping = findProportions(u_prime,v_prime)
                newval = getNewValue(mapping,img_b)

                warpedB[row,column] = newval
                overlay[row,column,1] = newval

    return (cv2.normalize(warpedB, alpha=0, beta=255, norm_type=cv2.NORM_MINMAX).astype(np.uint8),cv2.normalize(overlay, alpha=0, beta=255, norm_type=cv2.NORM_MINMAX).astype(np.uint8))

    pass
