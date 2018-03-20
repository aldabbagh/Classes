import math
import numpy as np
import cv2
import sys

# # Implement the functions below.


def extractRed(image):
    """ Returns the red channel of the input image. It is highly recommended to make a copy of the
    input image in order to avoid modifying the original array. You can do this by calling:
    temp_image = np.copy(image)

    Args:
        image (numpy.array): Input RGB (BGR in OpenCV) image.

    Returns:
        numpy.array: Output 2D array containing the red channel.
    """
    temp_image = np.copy(image)
    return temp_image[:,:,2]
    pass


def extractGreen(image):
    """ Returns the green channel of the input image. It is highly recommended to make a copy of the
    input image in order to avoid modifying the original array. You can do this by calling:
    temp_image = np.copy(image)

    Args:
        image (numpy.array): Input RGB (BGR in OpenCV) image.

    Returns:
        numpy.array: Output 2D array containing the green channel.
    """
    temp_image = np.copy(image)
    return temp_image[:,:,1]
    pass


def extractBlue(image):
    """ Returns the blue channel of the input image. It is highly recommended to make a copy of the
    input image in order to avoid modifying the original array. You can do this by calling:
    temp_image = np.copy(image)

    Args:
        image (numpy.array): Input RGB (BGR in OpenCV) image.

    Returns:
        numpy.array: Output 2D array containing the blue channel.
    """
    temp_image = np.copy(image)
    return temp_image[:,:,0]
    pass   


def swapGreenBlue(image):
    """ Returns an image with the green and blue channels of the input image swapped. It is highly
    recommended to make a copy of the input image in order to avoid modifying the original array.
    You can do this by calling:
    temp_image = np.copy(image)

    Args:
        image (numpy.array): Input RGB (BGR in OpenCV) image.

    Returns:
        numpy.array: Output 3D array with the green and blue channels swapped.
    """
    temp_image = np.copy(image)
    temp_result = np.copy(image)

    temp_result[:,:,0] = temp_image[:,:,1]
    temp_result[:,:,1] = temp_image[:,:,0]
    return temp_result
    pass


def copyPasteMiddle(src, dst, shape):
    """ Copies the middle region of size shape from src to the middle of dst. It is
    highly recommended to make a copy of the input image in order to avoid modifying the
    original array. You can do this by calling:
    temp_image = np.copy(image)

        Note: Assumes that src and dst are monochrome images, i.e. 2d arrays.

        Note: Where 'middle' is ambiguous because of any difference in the oddness
        or evenness of the size of the copied region and the image size, the function
        rounds downwards.  E.g. in copying a shape = (1,1) from a src image of size (2,2)
        into an dst image of size (3,3), the function copies the range [0:1,0:1] of
        the src into the range [1:2,1:2] of the dst.

    Args:
        src (numpy.array): 2D array where the rectangular shape will be copied from.
        dst (numpy.array): 2D array where the rectangular shape will be copied to.
        shape (tuple): Tuple containing the height (int) and width (int) of the section to be
                       copied.

    Returns:
        numpy.array: Output monochrome image (2D array)
    """
    width, height = shape
    temp_image_src = np.copy(src)
    temp_image_dst = np.copy(dst)
    #TODO: find middle of src (check if odd or even)
    src_row, src_column = temp_image_src.shape
    src_middle_row = src_row/2
    src_middle_column = src_column/2
    #TODO: find middle of dst (check if odd or even)
    dst_row, dst_column = temp_image_dst.shape
    dst_middle_row = dst_row/2
    dst_middle_column = dst_column/2

    dst_row1 = dst_middle_row-width/2
    dst_row2 = dst_row1+width
    dst_col1 = dst_middle_column-height/2
    dst_col2 = dst_col1+height

    src_row1 = src_middle_row-width/2
    src_row2 = src_row1+width
    src_col1 = src_middle_column-height/2
    src_col2 = src_col1+height

    new_portion = temp_image_src[src_row1:src_row2,src_col1:src_col2]
    new_row,new_col = new_portion.shape

    temp_image_dst[dst_row1:dst_row2,dst_col1:dst_col2] = temp_image_src[src_row1:src_row2,src_col1:src_col2]
    return temp_image_dst
    pass


def imageStats(image):
    """ Returns the tuple (min,max,mean,stddev) of statistics for the input monochrome image.
    In order to become more familiar with Numpy, you should look for pre-defined functions
    that do these operations i.e. numpy.min.

    It is highly recommended to make a copy of the input image in order to avoid modifying
    the original array. You can do this by calling:
    temp_image = np.copy(image)

    Args:
        image (numpy.array): Input 2D image.

    Returns:
        tuple: Four-element tuple containing:
               min (float): Input array minimum value.
               max (float): Input array maximum value.
               mean (float): Input array mean / average value.
               stddev (float): Input array standard deviation.
    """
    temp_image = np.copy(image)
    flattened = temp_image.flatten()
    return (float(np.min(flattened)),float(np.max(flattened)),np.mean(flattened),np.std(flattened))
    pass


def normalized(image, scale):
    """ Returns an image with the same mean as the original but with values scaled about the
    mean so as to have a standard deviation of "scale".

    Note: This function makes no defense against the creation
    of out-of-range pixel values.  Consider converting the input image to
    a float64 type before passing in an image.

    It is highly recommended to make a copy of the input image in order to avoid modifying
    the original array. You can do this by calling:
    temp_image = np.copy(image)

    Args:
        image (numpy.array): Input 2D image.
        scale (int or float): scale factor.

    Returns:
        numpy.array: Output 2D image.
    """
    temp_image = np.copy(image)
    temp_image = temp_image.astype(float)
    avg = np.mean(temp_image.flatten())
    std = np.std(temp_image.flatten())
    temp_image = ((temp_image-avg)*float(scale)/std)+avg
    return temp_image
    pass


def shiftImageLeft(image, shift):
    """ Outputs the input monochrome image shifted shift pixels to the left.

    The returned image has the same shape as the original with
    the BORDER_REPLICATE rule to fill-in missing values.  See

    http://docs.opencv.org/2.4/doc/tutorials/imgproc/imgtrans/copyMakeBorder/copyMakeBorder.html?highlight=copy

    for further explanation.

    It is highly recommended to make a copy of the input image in order to avoid modifying
    the original array. You can do this by calling:
    temp_image = np.copy(image)

    Args:
        image (numpy.array): Input 2D image.
        shift (int): Displacement value representing the number of pixels to shift the input image.
            This parameter may be 0 representing zero displacement.

    Returns:
        numpy.array: Output shifted 2D image.
    """
    if shift ==0:
        return image
    temp_image = np.copy(image)
    output_image = np.copy(image)
    output_image[:,0:-shift] = temp_image[:,shift:]
    for col in range(1,shift+1):
        output_image[:,-col] = temp_image[:,-1]
    return output_image



def differenceImage(img1, img2):
    """ Returns the difference between the two input images (img1 - img2). The resulting array must be normalized
    and scaled to fit [0, 255].

    It is highly recommended to make a copy of the input image in order to avoid modifying
    the original array. You can do this by calling:
    temp_image = np.copy(image)

    Args:
        img1 (numpy.array): Input 2D image.
        img2 (numpy.array): Input 2D image.

    Returns:
        numpy.array: Output 2D image containing the result of subtracting img2 from img1.
    """
    temp_image1 = np.copy(img1).astype(float)
    temp_image2 = np.copy(img2).astype(float)
    output = temp_image1-temp_image2
    new_dst = np.copy(temp_image1)
    cv2.normalize(output,dst=new_dst,alpha=0,beta=255,norm_type=cv2.NORM_MINMAX)
    return new_dst

    pass


def addNoise(image, channel, sigma):
    """ Returns a copy of the input color image with Gaussian noise added to
    channel (0-2). The Gaussian noise mean must be zero. The parameter sigma
    controls the standard deviation of the noise.

    The returned array values must not be clipped or normalized and scaled. This means that
    there could be values that are not in [0, 255].

    Note: This function makes no defense against the creation
    of out-of-range pixel values.  Consider converting the input image to
    a float64 type before passing in an image.

    It is highly recommended to make a copy of the input image in order to avoid modifying
    the original array. You can do this by calling:
    temp_image = np.copy(image)

    Args:
        image (numpy.array): input RGB (BGR in OpenCV) image.
        channel (int): Channel index value.
        sigma (float): Gaussian noise standard deviation.

    Returns:
        numpy.array: Output 3D array containing the result of adding Gaussian noise to the
            specified channel.
    """
    temp_image = np.copy(image)
    width, height, channels = temp_image.shape
    noise_signal = np.random.randn(width, height)*sigma
    temp_image[:,:,channel] = temp_image[:,:,channel] + noise_signal
    return temp_image
    pass
