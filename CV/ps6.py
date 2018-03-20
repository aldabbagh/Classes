"""Problem Set 6: Optic Flow."""

import numpy as np
import cv2


# Utility function
def normalize_and_scale(image_in, scale_range=(0, 255)):
    """Normalizes and scales an image to a given range [0, 255].

    Utility function. There is no need to modify it.

    Args:
        image_in (numpy.array): input image.
        scale_range (tuple): range values (min, max). Default set to [0, 255].

    Returns:
        numpy.array: output image (uint8).
    """
    image_out = np.zeros(image_in.shape)
    cv2.normalize(image_in, image_out, alpha=scale_range[0], beta=scale_range[1], norm_type=cv2.NORM_MINMAX)

    return image_out


# Assignment code
def gradient_x(image):
    """Computes image gradient in X direction.

    Use cv2.Sobel to help you in this function. Additionally you should set cv2.Sobel's 'scale' parameter to
    one eighth.

    Args:
        image (numpy.array): grayscale floating-point image with values in [0.0, 1.0].

    Returns:
        numpy.array: image gradient in the X direction. Output from cv2.Sobel.
    """

    grad =  cv2.Sobel(image, cv2.CV_64F, 1, 0, ksize=3, scale= 1./8.).astype(np.float64)
    normalized = cv2.normalize(grad,alpha=-1,beta=1,norm_type=cv2.NORM_MINMAX)
    return normalized
    pass


def gradient_y(image):
    """Computes image gradient in Y direction.

    Use cv2.Sobel to help you in this function. Additionally you should set cv2.Sobel's 'scale' parameter to
    one eighth.

    Args:
        image (numpy.array): grayscale floating-point image with values in [0.0, 1.0].

    Returns:
        numpy.array: image gradient in the Y direction. Output from cv2.Sobel.
    """

    grad =  cv2.Sobel(image, cv2.CV_64F, 0, 1, ksize=3, scale= 1./8.).astype(np.float64)
    normalized = cv2.normalize(grad,alpha=-1,beta=1,norm_type=cv2.NORM_MINMAX)
    return normalized
    pass


def optic_flow_lk(img_a, img_b, k_size, k_type, sigma=1):
    """Computes optic flow using the Lucas-Kanade method.

    For efficiency, you should apply a convolution-based method similar to the approach used
    in the last problem sets.

    Note: Implement this method using the instructions in the lectures and the documentation.

    You are not allowed to use any OpenCV functions that are related to Optic Flow.

    Args:
        img_a (numpy.array): grayscale floating-point image with values in [0.0, 1.0].
        img_b (numpy.array): grayscale floating-point image with values in [0.0, 1.0].
        k_size (int): size of averaging kernel to use for weighted averages. Here we assume the kernel window is a
                      square so you will use the same value for both width and height.
        k_type (str): type of kernel to use for weighted averaging, 'uniform' or 'gaussian'. By uniform we mean a
                      kernel with the only ones divided by k_size**2. To implement a Gaussian kernel use
                      cv2.getGaussianKernel. The autograder will use 'uniform'.
        sigma (float): sigma value if gaussian is chosen. Default value set to 1 because the autograder does not use
                       this parameter.

    Returns:
        tuple: 2-element tuple containing:
            U (numpy.array): raw displacement (in pixels) along X-axis, same size as the input images, floating-point
                             type.
            V (numpy.array): raw displacement (in pixels) along Y-axis, same size and type as U.
    """
    kernel_dim = [k_size]*2
    img1 = np.copy(img_a)
    img2 = np.copy(img_b)
    factor = 3
    if k_type == 'uniform':
        # Generate a uniform kernel. The autograder uses this flag.
        pass
    elif k_type == 'gaussian':
        # Generate a gaussian kernel. This flag is not tested but may yield better results in some images.
        pass

    # Place your LK code here.
    def applyUniformKernel(array,krnl_dim):
        ddepth = -1
        y_len = array.shape[0]
        x_len = array.shape[1]
        kernel =np.ones(krnl_dim)/(krnl_dim[0]*krnl_dim[1])
        array_padded = cv2.copyMakeBorder(array,int(np.floor(krnl_dim[0]/2)),int(np.floor(krnl_dim[0]/2)),int(np.floor(krnl_dim[1]/2)),int(np.floor(krnl_dim[1]/2)),cv2.BORDER_REPLICATE)
        output = cv2.filter2D(array_padded,ddepth,kernel)[int(np.floor(krnl_dim[0]/2)):int(np.floor(krnl_dim[0]/2))+y_len, int(np.floor(krnl_dim[1]/2)):int(np.floor(krnl_dim[1]/2))+x_len]
        return output

    def findDeterminant(grad_x,grad_y,krnl_dim):
        temp = applyUniformKernel(grad_x*grad_x,krnl_dim)*applyUniformKernel(grad_y*grad_y,krnl_dim)\
               -applyUniformKernel(grad_x*grad_y,krnl_dim)*applyUniformKernel(grad_y*grad_x,krnl_dim)
        for i in range(len(temp)):
            for j in range(len(temp[0])):
                if temp[i,j] < 0.00000000001:
                    temp[i,j] = 10000000000.0
        return temp


    #find gradients
    grad_x = gradient_x(img1)
    grad_y = gradient_y(img1)

    matrix = [applyUniformKernel(grad_x*grad_x,kernel_dim),applyUniformKernel(grad_x*grad_y,kernel_dim)\
               ,applyUniformKernel(grad_y*grad_x,kernel_dim),applyUniformKernel(grad_y*grad_y,kernel_dim)]
    determinant = findDeterminant(grad_x,grad_y,kernel_dim)

    x = -applyUniformKernel(grad_x*(img2-img1),kernel_dim)
    y = -applyUniformKernel(grad_y*(img2-img1),kernel_dim)

    U = (matrix[3]*x/determinant - matrix[1]*y/determinant)
    V = (matrix[0]*y/determinant - matrix[2]*x/determinant)
    return (factor*U,factor*V)
    pass


def reduce_image(image):
    """Reduces an image to half its shape.

    The autograder will pass images with even width and height. When dealing with odd dimensions, the output image
    should be the result of rounding up the division by 2. For example (width, height): (13, 19) -> (7, 10)

    For simplicity and efficiency, implement a convolution-based method using the 5-tap separable filter.

    Follow the process shown in the lecture 6B-L3. Also refer to:
    -  Burt, P. J., and Adelson, E. H. (1983). The Laplacian Pyramid as a Compact Image Code
    You can find the link in the problem set instructions.

    Args:
        image (numpy.array): grayscale floating-point image, values in [0.0, 1.0].

    Returns:
        numpy.array: output image with half the shape, same type as the input image.
    """
    img1 = np.copy(image)
    ddepth = -1
    k = [[ 0.0625,  0.25,    0.375,   0.25, 0.0625]]
    reducing_kernel = np.dot(np.asarray(k).T,np.asarray(k))
    out = cv2.filter2D(img1,ddepth,reducing_kernel)
    even = out[[slice(None, None, 2) for _ in range(out.ndim)]]
    return even
    pass


def gaussian_pyramid(image, levels):
    """Creates a Gaussian pyramid of a given image.

    This method uses reduce_image() at each level. Each image is stored in a list of length equal the number of levels.
    The first element in the list ([0]) should contain the input image. All other levels contain a reduced version
    of the previous level.

    All images in the pyramid should floating-point with values in

    Args:
        image (numpy.array): grayscale floating-point image, values in [0.0, 1.0].
        levels (int): number of levels in the resulting pyramid.

    Returns:
        list: Gaussian pyramid, list of numpy.arrays.
    """
    GaussianPyramid = []
    GaussianPyramid.append(image)
    for i in range(levels-1):
        temp = reduce_image(GaussianPyramid[-1])
        GaussianPyramid.append(temp)
    return GaussianPyramid
    pass


def create_combined_img(img_list):
    """Stacks images from the input pyramid list side-by-side, large to small from left to right.

    See the problem set instructions 2a. for a reference on how the output should look like.

    Make sure you call normalize_and_scale() for each image in the pyramid when populating img_out.

    Args:
        img_list (list): list with pyramid images.

    Returns:
        numpy.array: output image with the pyramid images stacked from left to right.
    """
    output = normalize_and_scale(img_list[0])
    number_of_images = len(img_list)
    dimMain = output.shape[0]
    for i in range(1,number_of_images):
        temp = img_list[i]
        current = normalize_and_scale(temp)
        dim1 = current.shape[0]
        dim2 = current.shape[1]
        temp2 = np.append(current,np.zeros((dimMain-dim1,dim2)),axis=0)
        output = np.append(output,temp2,axis=1)

    return output
    pass


def expand_image(image):
    """Expands an image doubling its width and height.

    For simplicity and efficiency, implement a convolution-based method using the 5-tap separable filter.

    Follow the process shown in the lecture 6B-L3. Also refer to:
    -  Burt, P. J., and Adelson, E. H. (1983). The Laplacian Pyramid as a Compact Image Code
    You can find the link in the problem set instructions.

    Args:
        image (numpy.array): grayscale floating-point image, values in [0.0, 1.0].

    Returns:
        numpy.array: same type as 'image' with the doubled height and width.
    """
    img1 = np.copy(image)
    x = 2*img1.shape[0]
    y = 2*img1.shape[1]
    temp_img = np.zeros((x,y))
    temp_img[[slice(None, None, 2) for _ in range(temp_img.ndim)]] = img1
    ddepth = -1
    k = [[ 0.125,  0.5, 0.75,  0.5, 0.125]]
    my_kernel = np.dot(np.asarray(k).T,np.asarray(k))
    out = cv2.filter2D(temp_img,ddepth,my_kernel)
    return out
    #even = out[[slice(None, None, 2) for _ in range(out.ndim)]]

    pass


def laplacian_pyramid(g_pyr):
    """Creates a Laplacian pyramid from a given Gaussian pyramid.

    This method uses expand_image() at each level.

    Args:
        g_pyr (list): Gaussian pyramid, returned by gaussian_pyramid().

    Returns:
        list: Laplacian pyramid, with l_pyr[-1] = g_pyr[-1].
    """
    laplacian = []
    numImgs = len(g_pyr)
    for i in range(numImgs-1):
        current = g_pyr[i]
        next = g_pyr[i+1]
        expanded = expand_image(next)
        diff=current-expanded
        laplacian.append(diff)
    last = g_pyr[len(g_pyr)-1]
    laplacian.append(last)
    return laplacian
    pass


def warp(image, U, V, interpolation, border_mode):
    """Warps image using X and Y displacements (U and V).

    This function uses cv2.remap. The autograder will use cubic interpolation and the BORDER_REFLECT101 border mode.
    You may change this to work with the problem set images.

    See the cv2.remap documentation to read more about border and interpolation methods.

    Args:
        image (numpy.array): grayscale floating-point image, values in [0.0, 1.0].
        U (numpy.array): displacement (in pixels) along X-axis.
        V (numpy.array): displacement (in pixels) along Y-axis.
        interpolation (Inter): interpolation method used in cv2.remap.
        border_mode (BorderType): pixel extrapolation method used in cv2.remap.

    Returns:
        numpy.array: warped image, such that warped[y, x] = image[y + V[y, x], x + U[y, x]]
    """
    A = np.copy(image).astype(np.float32)
    dim1 = xrange(A.shape[1])
    dim2 = xrange(A.shape[0])

    newmesh = np.meshgrid(dim1,dim2)
    shifted_x = newmesh[0]+U
    shifted_y = newmesh[1]+V
    warped = cv2.remap(A,shifted_x.astype(np.float32),shifted_y.astype(np.float32),interpolation=interpolation,borderMode=border_mode)
    return warped

    pass


def hierarchical_lk(img_a, img_b, levels, k_size, k_type, sigma, interpolation, border_mode):
    """Computes the optic flow using the Hierarchical Lucas-Kanade method.

    Refer to the problem set documentation to find out the steps involved in this function.

    This method should use reduce_image(), expand_image(), warp(), and optic_flow_lk().

    Args:
        img_a (numpy.array): grayscale floating-point image, values in [0.0, 1.0].
        img_b (numpy.array): grayscale floating-point image, values in [0.0, 1.0].
        levels (int): Number of levels.
        k_size (int): parameter to be passed to optic_flow_lk.
        k_type (str): parameter to be passed to optic_flow_lk.
        sigma (float): parameter to be passed to optic_flow_lk.
        interpolation (Inter): parameter to be passed to warp.
        border_mode (BorderType): parameter to be passed to warp.

    Returns:
        tuple: 2-element tuple containing:
            U (numpy.array): raw displacement (in pixels) along X-axis, same size as the input images, floating-point
                             type.
            V (numpy.array): raw displacement (in pixels) along Y-axis, same size and type as U.
    """
    # https://piazza.com/class/ixpb4h3cvua2gp?cid=467
    Aks = gaussian_pyramid(img_a,levels)
    Bks = gaussian_pyramid(img_b,levels)
    highest_level = len(Aks)-1
    k_range = range(0,highest_level+1)
    k_range.reverse()
    for k in k_range:
        Ak = Aks[k]
        Bk = Bks[k]
        if k==highest_level:
            U = np.zeros(Ak.shape)
            V = np.zeros(Bk.shape)
        else:
            U = 2*expand_image(U)
            V = 2*expand_image(V)


        Ck = warp(Bk,U,V,interpolation,border_mode)
        (dx,dy) = optic_flow_lk(Ak,Ck,k_size,k_type,sigma)
        U = U+dx
        V = V+dy

    return (U,V)
    pass
