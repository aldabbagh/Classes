"""Problem Set 2: Edges and Lines."""

import math
import numpy as np
import cv2


def hough_lines_acc(img_edges, rho_res, theta_res):
    """ Creates and returns a Hough accumulator array by computing the Hough Transform for lines on an
    edge image.

    This method defines the dimensions of the output Hough array using the rho and theta resolution
    parameters. The Hough accumulator is a 2-D array where its rows and columns represent the index
    values of the vectors rho and theta respectively. The length of each dimension is set by the
    resolution parameters. For example: if rho is a vector of values that are in [0, a_1, a_2, ... a_n],
    and rho_res = 1, rho should remain as [0, a_1, a_2, ... , a_n]. If rho_res = 2, then rho would
    be half its length i.e [0, a_2, a_4, ... , a_n] (assuming n is even). The same description applies
    to theta_res and the output vector theta. These two parameters define the size of each bin in
    the Hough array.

    Note that indexing using negative numbers will result in calling index values starting from
    the end. For example, if b = [0, 1, 2, 3, 4] calling b[-2] will return 3.

    Args:
        img_edges (numpy.array): edge image (every nonzero value is considered an edge).
        rho_res (int): rho resolution (in pixels).
        theta_res (float): theta resolution (in degrees converted to radians i.e 1 deg = pi/180).

    Returns:
        tuple: Three-element tuple containing:
               H (numpy.array): Hough accumulator array.
               rho (numpy.array): vector of rho values, one for each row of H
               theta (numpy.array): vector of theta values, one for each column of H.
    """
    def getRhoIndex(d, rho_array):
        index = 0
        for element in rho_array:
            if d>=element:
                return index
            index +=1
        return index

    rho_max = np.sqrt((img_edges.shape[0]-1)**2+(img_edges.shape[1]-1)**2) #Bins
    RHO_list = np.asarray(range(0,int(rho_max),int(rho_res)))
    rho_increment = RHO_list[1]-RHO_list[0]
    THETA_list = np.asarray(range(0,360,int(np.rad2deg(theta_res))))
    H = np.zeros((
                    len(RHO_list),
                    len(THETA_list)
                ))

    for x1 in range(img_edges.shape[0]):
        for y1 in range(img_edges.shape[1]):
            if img_edges[x1,y1]>0:
                for ang_index in range(len(THETA_list)):
                    theta = np.deg2rad(THETA_list[ang_index])
                    rho = np.ceil(y1*np.cos(theta)+x1*np.sin(theta))
                    H[int((rho/rho_res)),ang_index] +=1
    return (H, RHO_list , THETA_list)
    pass


def hough_peaks(H, hough_threshold, nhood_delta, rows=None, cols=None):
    """Returns the best peaks in a Hough Accumulator array.

    This function selects the pixels with the highest intensity values in an area and returns an array
    with the row and column indices that correspond to a local maxima. This search will only look at pixel
    values that are greater than or equal to hough_threshold.

    Part of this function performs a non-maxima suppression using the parameter nhood_delta which will
    indicate the area that a local maxima covers. This means that any other pixels, with a non-zero values,
    that are inside this area will not be counted as a peak eliminating possible duplicates. The
    neighborhood is a rectangular area of shape nhood_delta[0] * 2 by nhood_delta[1] * 2.

    When working with hough lines, you may need to use the true value of the rows and columns to suppress
    duplicate values due to aliasing. You can use the rows and cols parameters to access the true value of
    for rho and theta at a specific peak.

    Args:
        H (numpy.array): Hough accumulator array.
        hough_threshold (int): minimum pixel intensity value in the accumulator array to search for peaks
        nhood_delta (tuple): a pair of integers indicating the distance in the row and
                             column indices deltas over which non-maximal suppression should take place.
        rows (numpy.array): array with values that map H rows. Default set to None.
        cols (numpy.array): array with values that map H columns. Default set to None.

    Returns:
        numpy.array: Output array of shape Q x 2 where each row is a [row_id, col_id] pair
                     where the peaks are in the H array and Q is the number of the peaks found in H.
    """
    # In order to standardize the range of hough_threshold values let's work with a normalized version of H.
    Output = []
    H_norm = cv2.normalize(H.copy(), alpha=0, beta=255, norm_type=cv2.NORM_MINMAX)
    H_norm [H_norm < hough_threshold] = 0
    x_nhood = nhood_delta[0]
    y_nhood = nhood_delta[1]
    finished = False
    while not finished:
        temp = np.unravel_index(H_norm.argmax(), H_norm.shape)
        row_id = temp[0]
        col_id = temp[1]
        # find bounds of range to zero
        if row_id-x_nhood<0:
            x_start = 0
        else:
            x_start = row_id-x_nhood

        if row_id+x_nhood>np.shape(H_norm)[0]:
            x_end = np.shape(H_norm)[0]
        else:
            x_end =row_id+x_nhood

        if col_id-y_nhood<0:
            y_start = 0
        else:
            y_start = col_id-y_nhood

        if col_id+y_nhood>np.shape(H_norm)[1]:
            y_end = np.shape(H_norm)[1]
        else:
            y_end =col_id+y_nhood

        if H_norm[row_id,col_id]>0:
            Output.append(list(temp))

        if H_norm[row_id,col_id]==0:
            finished = True

        #Zero the neighboorhood Area
        H_norm[x_start:x_end,y_start:y_end] = 0



    return np.asarray(Output)


    # Once you have all the detected peaks, you can eliminate the ones that represent
    # the same line. This will only be helpful when working with Hough lines.
    # The autograder does not pass these parameters when using a Hough circles array because it is not
    # needed. You can opt out from implementing this part, make sure you comment it out or delete it.
    #if rows is not None and cols is not None:
        # Aliasing Suppression.
    #    pass

    pass


def hough_circles_acc(img_orig, img_edges, radius, point_plus=True):
    """Returns a Hough accumulator array using the Hough Transform for circles.

    This function implements two methods: 'single point' and 'point plus'. Refer to the problem set
    instructions and the course lectures for more information about them.

    For simplicity purposes, this function returns an array of the same dimensions as img_edges.
    This means each bin corresponds to one pixel (there are no changes to the grid discretization).

    Note that the 'point plus' method requires gradient images in X and Y (see cv2.Sobel) using
    img_orig to perform the voting.

    Args:
        img_orig (numpy.array): original image.
        img_edges (numpy.array): edge image (every nonzero value is considered an edge).
        radius (int): radius value to look for.
        point_plus (bool): flag that allows to choose between 'single point' or 'point plus'.

    Returns:
        ===
        numpy.array: Hough accumulator array.
    """
    r = radius
    new_image = np.copy(img_edges)
    dim1 = new_image.shape[0]
    dim2 = new_image.shape[1]
    H = np.zeros((dim1,dim2))
    x_grad = cv2.Sobel(img_orig, cv2.CV_8U, 1, 0)
    y_grad = cv2.Sobel(img_orig, cv2.CV_8U, 0, 1)

    if not point_plus:
        THETA_array = np.linspace(0,360,num=dim2)
        for y1 in range(dim2):
            for x1 in range(dim1):
                if new_image[x1,y1]!= 0:
                    for i in range(len(THETA_array)):
                        theta = np.deg2rad(THETA_array[i])
                        #theta = (THETA_array[i])
                        a = np.ceil(x1-r*np.cos(theta))
                        b = np.ceil(y1+r*np.sin(theta))
                        if a>=0 and b>=0 and a<dim1 and b<dim2:
                            H[int(a),int(b)]+=1
        return H

    else:
        for y1 in range(dim1):
            for x1 in range(dim2):
                if img_edges[y1,x1]!= 0:
                    theta_begin_rad = np.arctan2(y_grad[y1,x1],x_grad[y1,x1])
                    theta_begin_deg = np.rad2deg(theta_begin_rad)
                    theta_list = [theta_begin_deg, theta_begin_deg]
                    for i in range(len(theta_list)):
                        theta = np.deg2rad(theta_list[i])
                        if i==0:
                            a = np.round(x1+r*np.cos(theta))
                            b = np.round(y1+r*np.sin(theta))
                            if a>=0 and b>=0 and a<dim2 and b<dim1:
                                H[int(b),int(a)]+=1
                        else:
                            a = np.round(x1-r*np.cos(theta))
                            b = np.round(y1-r*np.sin(theta))
                            if a>=0 and b>=0 and a<dim2 and b<dim1:
                                H[int(b),int(a)]+=1
        return H
    pass



def find_circles(img_orig, edge_img, radii, hough_threshold, nhood_delta):
    """Finds circles in the input edge image using the Hough transform and the point plus gradient
    method.

    In this method you will call both hough_circles_acc and hough_peaks.

    The goal here is to call hough_circles_acc iterating over the values in 'radii'. A Hough accumulator
    is generated for each radius value and the respective peaks are identified. It is recommended that
    the peaks from all radii are stored with their respective vote value. That way you can identify which
    are true peaks and discard false positives.

    Args:
        img_orig (numpy.array): original image. Pass this parameter to hough_circles_acc.
        edge_img (numpy.array): edge image (every nonzero value is considered an edge).
        radii (list): list of radii values to search for.
        hough_threshold (int): minimum pixel intensity value in the accumulator array to
                               search for peaks. Pass this value to hough_peaks.
        nhood_delta (tuple): a pair of integers indicating the distance in the row and
                             column indices deltas over which non-maximal suppression should
                             take place. Pass this value to hough_peaks.

    Returns:
        numpy.array: array with the circles position and radius where each row
                     contains [row_id, col_id, radius]
    """
    def euclid(x1,x2,y1,y2):
        result = np.sqrt((x1-x2)**2+(y1-y2)**2)
        return result

    def initialize_output():
        output = np.expand_dims(np.asarray([1, 1, 1, 1]), axis=0)
        return output

    def update_output(output,peaks,H):
        r_col = [r for i in range(len(peaks))]
        peak_vals = []
        for peak in peaks:
            peak_vals.append(H[peak[0],peak[1]])
        peak_vals = np.asarray(peak_vals)
        output = np.row_stack((output,np.column_stack([peaks,r_col,peak_vals])))
        return output

    output = initialize_output()

    for r in radii:
        temp =hough_circles_acc(img_orig, edge_img, r, point_plus=True)
        H_n = cv2.normalize(temp, alpha=0, beta=255, norm_type=cv2.NORM_MINMAX, dtype=8)
        peaks = hough_peaks(H_n, hough_threshold, nhood_delta)

        new_peaks = []
        for i in range(len(peaks)):
            if H_n[peaks[i][0],peaks[i][1]]>= 0.70*255:
                new_peaks.append(peaks[i])
        output = update_output(output,new_peaks,H_n)

        #output = update_output(output,peaks,H_n)

    new_out = []
    suppressed = []
    for i in range(len(output)):
        for j in range(len(output)):
            if i not in suppressed and j not in suppressed and i!=j:
                x1 = output[i][1]
                y1 = output[i][0]
                r1 = output[i][2]

                x2 = output[j][1]
                y2 = output[j][0]
                r2 = output[j][2]
                if euclid(x1,x2,y1,y2)<0.95*(r1+r2):
                    if r1>r2:
                        suppressed.append(j)
                    else:
                        suppressed.append(i)

    for idx in range(len(output)):
        if idx not in suppressed:
            new_out.append(output[idx])

    output = np.asarray(new_out)

    sorted_output = output[output[:,3].argsort()]
    return sorted_output[1:, :-1][::-1]

    #return output[1:, :-1]


    pass




