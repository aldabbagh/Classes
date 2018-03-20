import numpy as np
import matplotlib.pyplot as plt
import cv2


def imshow(image, to_rgb=True, force_uint8=True):
    '''
    Shows an image using matplotlib.

    Parameters
    ----------
    image: numpy.ndarray
        Either a grayscale or color image
    '''
    if force_uint8 and not image.dtype == np.uint8:
        image = image.astype(np.uint8)
    if len(image.shape) == 3:
        if to_rgb:
            image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        plt.imshow(image)
    else:
        plt.imshow(image, cmap='gray')
    plt.show()

