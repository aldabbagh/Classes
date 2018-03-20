import numpy as np
import cv2


def solve_least_squares(pts3d, pts2d):
    """Solves for the transformation matrix M that maps each 3D point to corresponding 2D point
    using the least-squares method. See np.linalg.lstsq.

    Args:
        pts3d (numpy.array): 3D global (x, y, z) points of shape (N, 3). Where N is the number of points.
        pts2d (numpy.array): corresponding 2D (u, v) points of shape (N, 2). Where N is the number of points.

    Returns:
        tuple: two-element tuple containing:
               M (numpy.array): transformation (a.k.a. projection) matrix of shape (3, 4).
               error (float): sum of squared residuals of all points.
    """
    number_of_points = len(pts3d)
    A = []
    B = []

    #construct A and B matrices to calculte M using (A x M = B)
    for i in range(number_of_points):
        X,Y,Z = pts3d[i]
        u,v = pts2d[i]
        #This is as described in office hours Feb. 21 https://www.youtube.com/watch?v=K3YfaeG6ZuU
        A.append([X,Y,Z,1,0,0,0,0,-u*X,-u*Y,-u*Z])
        A.append([0,0,0,0,X,Y,Z,1,-v*X,-v*Y,-v*Z])

        #Add u,v values to B in alteration
        B.append(u)
        B.append(v)

    A = np.asarray(A)
    B = np.asarray(B).transpose()

    #Apply linalg.lstsq https://docs.scipy.org/doc/numpy/reference/generated/numpy.linalg.lstsq.html
    #returns solution(M), residuals, rank, s

    solution = np.linalg.lstsq(A,B) #This gets us values of m from m00 to m22
    M = np.append(solution[0],[1.0])*(-0.5968000) #First we add the value of m23 (which is 1.0) then this multiplied by the value in the HW M[2,3]
    residuals = solution[1][0]
    return M.reshape((3,4)),residuals
    pass


def project_points(pts3d, m):
    """Projects each 3D point to 2D using the matrix M.

    Args:
        pts3d (numpy.array): 3D global (x, y, z) points of shape (N, 3). Where N is the number of points.
        m (numpy.array): transformation (a.k.a. projection) matrix of shape (3, 4).

    Returns:
        numpy.array: projected 2D (u, v) points of shape (N, 2). Where N is the same as pts3d.
    """

    numPoints = len(pts3d)
    newPts3d = np.zeros((numPoints,4))
    #add ones to the points
    for i in range(numPoints):
        point = pts3d[i]
        newPoint = np.append(point,1.0)
        newPts3d[i] =newPoint

    #projected 2D (u, v) points of shape (N, 2). Where N is the same as pts3d.
    projectedPoints = np.zeros((numPoints,2))
    i =0

    for point in newPts3d:
        #calculate product
        p = np.dot(m,point) # this gets us [u',v',s]
        #divide u and v by the last value (s) to get [u, v, 1]
        u = p[0]/p[2]
        v = p[1]/p[2]
        projectedPoints[i]=[u,v]
        i+=1

    return projectedPoints
    pass


def get_residuals(pts2d, pts2d_projected):
    """Computes residual error for each point.

    Args:
        pts2d (numpy.array): observed 2D (u, v) points of shape (N, 2). Where N is the number of points.
        pts2d_projected (numpy.array): 3D global points projected to 2D of shape (N, 2).
                                       Where N is the number of points.

    Returns:
        numpy.array: residual error for each point (L2 distance between each observed and projected 2D points).
                     The array shape must be (N, 1). Where N is the same as in pts2d and pts2d_projected.
    """
    def get_l2_distance(pt1, pt2):
        # http://stackoverflow.com/questions/1401712/how-can-the-euclidean-distance-be-calculated-with-numpy
        dist = np.linalg.norm(pt1-pt2)
        return dist

    numberOfPoints = len(pts2d)
    residualErr = []
    for i in range(numberOfPoints):
        distance = get_l2_distance(pts2d[i],pts2d_projected[i])
        residualErr.append([distance])

    return np.asarray(residualErr)
    pass


def calibrate_camera(pts3d, pts2d, set_size_k):
    """Finds the best camera projection matrix given corresponding 3D and 2D points.

    Args:
        pts3d (numpy.array): 3D global (x, y, z) points of shape (N, 3). Where N is the number of points.
        pts2d (numpy.array): corresponding 2D (u, v) points of shape (N, 2). Where N is the number of points.
        set_size_k (int): set of k random points to choose from pts2d.

    Returns:
        tuple: three-element tuple containing:
               bestM (numpy.array): best transformation matrix M of shape (3, 4).
               error (float): sum of squared residuals of all points for bestM.
               avg_residuals (numpy.array): Average residuals array, one row for each iteration.
                                            The array should be of shape (10, 1).
    """

    """
        - Randomly choose k points from the 2D list and their corresponding points in the 3D list.
        - Compute the projection matrix M on the chosen points.
        - Pick 4 points not in your set of k, and compute the average residual.
        - Return the M that gives the lowest residual.
    """
    def compute_average_residual(pts2dProjected, points2d):
        residuals = get_residuals(points2d,pts2dProjected)
        return np.mean(residuals)

    numberOfPoints = len(pts3d)
    concatTemp = np.zeros((numberOfPoints,5))
    concatTemp[:,0:3] = pts3d
    concatTemp[:,3:] = pts2d

    mPairs = []
    avg_residuals = []
    for i in range(10):
        np.random.shuffle(concatTemp)
        # Randomly choose k points from the 2D list and their corresponding points in the 3D list.
        k_points = concatTemp[:set_size_k,:]
        chosen3d = k_points[:,0:3]
        chosen2d = k_points[:,3:]

        # Compute the projection matrix M on the chosen points.
        M,residuals = solve_least_squares(chosen3d,chosen2d)
        pts2dProjected = project_points(chosen3d,M)

        # Pick 4 points not in your set of k
        otherfour = concatTemp[set_size_k:set_size_k+4,:]
        other3d = otherfour[:,0:3]
        other2d = otherfour[:,3:]

        # Compute the average residual
        averageResidualErr = compute_average_residual(pts2dProjected,other2d)

        mPairs.append([M,averageResidualErr])
        avg_residuals.append([averageResidualErr])

    # Return the M that gives the lowest residual.
    output = mPairs[0][0]
    lowestResidual = mPairs[0][1]
    for i in range(len(mPairs)):
        err = mPairs[i][1]
        if err<lowestResidual:
            lowestResidual = err
            output = mPairs[i][0]

    return (output,lowestResidual,np.asarray(avg_residuals))
    pass


def get_camera_center(m):
    """Finds the camera global coordinates.

    Args:
        m (numpy.array): transformation (a.k.a. projection) matrix of shape (3, 4).

    Returns:
        numpy.array: [x, y, z] camera coordinates. Array must be of shape (1, 3).
    """
    # M = [Q|m4]
    # C = -Q^-1 m4
    Q = m[:,:3]
    m4 = m[:,3]
    C = np.dot(-1.0*np.linalg.inv(Q),m4)
    C = np.asarray([[C[0],C[1],C[2]]])
    return C
    pass


def compute_fundamental_matrix(pts2d_1, pts2d_2):
    """Computes the fundamental matrix given corresponding points from 2 images of a scene.

    This function uses the least-squares method, see numpy.linalg.lstsq.

    Args:
        pts2d_1 (numpy.array): 2D points from image 1 of shape (N, 2). Where N is the number of points.
        pts2d_2 (numpy.array): 2D points from image 2 of shape (N, 2). Where N is the number of points.

    Returns:
        numpy.array: array containing the fundamental matrix elements. Array must be of shape (3, 3).
    """
    number_of_points = len(pts2d_1)
    A = []
    B = []

    #construct A and B matrices to calculte M using (A x M = B)
    for i in range(number_of_points):
        u_prime,v_prime = pts2d_1[i]
        u,v = pts2d_2[i]
        #This is as described in http://www.umiacs.umd.edu/~ramani/cmsc828d/lecture27.pdf without the last 1
        A.append([u_prime*u, u_prime*v, u_prime, v_prime*u, v_prime*v, v_prime, u, v])

        #Add values to B as mentioned in https://piazza.com/class/ixpb4h3cvua2gp?cid=321 by Anand Arya
        B.append(-1.0)

    A = np.asarray(A)
    B = np.asarray(B).transpose()

    #Apply linalg.lstsq https://docs.scipy.org/doc/numpy/reference/generated/numpy.linalg.lstsq.html
    #returns solution(M), residuals, rank, s

    solution = np.linalg.lstsq(A,B) #This gets us values of m from m00 to m22
    M = np.append(solution[0],[1.0])  #First we add the value of m23 (which is 1.0) then this multiplied by the value in the HW M[2,3]
    residuals = solution[1][0]

    return np.transpose(M.reshape((3,3)))

    pass


def reduce_rank(f):
    """Reduces a full rank (3, 3) matrix to rank 2.

    Args:
        f (numpy.array): full rank fundamental matrix. Must be a (3, 3) array.

    Returns:
        numpy.array: rank 2 fundamental matrix. Must be a (3, 3) array.
    """
    #find SVD
    U,Sigma,Vt = np.linalg.svd(f)

    #set smaller value of sigma to 0
    Sigma[np.argmin(Sigma)] = 0

    #reconstruct and return
    S = np.diag(Sigma)  #this is as suggested by the numpy documentation
    return np.dot(U, np.dot(S, Vt))
    pass


def get_epipolar_lines(img1_shape, img2_shape, f, pts2d_1, pts2d_2):
    """Returns epipolar lines using the fundamental matrix and two sets of 2D points.

    Args:
        img1_shape (tuple): image 1 shape (rows, cols)
        img2_shape (tuple): image 2 shape (rows, cols)
        f (numpy.array): Fundamental matrix of shape (3, 3).
        pts2d_1 (numpy.array): 2D points from image 1 of shape (N, 2). Where N is the number of points.
        pts2d_2 (numpy.array): 2D points from image 2 of shape (N, 2). Where N is the number of points.

    Returns:
        tuple: two-element tuple containing:
               epipolar_lines_1 (list): epipolar lines for image 1. Each list element should be
                                        [(x1, y1), (x2, y2)] one for each of the N points.
               epipolar_lines_2 (list): epipolar lines for image 2. Each list element should be
                                        [(x1, y1), (x2, y2)] one for each of the N points.
    """
    def getNewPoints(lr,ll,product):
        p1x,p1y,p1z = np.cross(product,ll)
        p2x,p2y,p2z = np.cross(product,lr)
        p1 = ( int(p1x/p1z),int(p1y/p1z))
        p2 = (int(p2x/p2z),int(p2y/p2z))
        return [p1,p2]

    def getLine(shape, pts, f):
        result = []
        #upper coordinates
        Pur = [shape[1]-1,0]
        Pul = [0,0]
        #lower coordinates
        Pbr = [shape[1]-1,shape[0]-1]
        Pbl = [0,shape[0]-1]
        lr= np.cross( np.append(np.asarray(Pur),1),np.append(np.asarray(Pbr),1)) #change to homogonous and get cross product
        ll= np.cross( np.append(np.asarray(Pul),1),np.append(np.asarray(Pbl),1)) #change to homogonous and get cross product
        homogonous = []
        for i in range(len(pts)):
            homogonous.append([pts[i,0],pts[i,1],1])
        homogonous = np.asarray(homogonous).transpose()
        product = np.dot(f,homogonous)
        i = 0
        for point in pts:
            newpoints = getNewPoints(lr,ll,product[:,i])
            result.append(newpoints)
            i+=1
        return result

    return getLine(img2_shape,pts2d_2,np.transpose(f)),getLine(img1_shape,pts2d_1,f)
    pass


def compute_t_matrix(pts2d):
    """Computes the transformation matrix T given corresponding 2D points from an image.

    Args:
        pts2d (numpy.array): corresponding 2D (u, v) points of shape (N, 2). Where N is the number of points.

    Returns:
        numpy.array: transformation matrix T of shape (3, 3).
    """
    s = 1/(np.max(np.abs(pts2d)))
    m1 = np.asarray([[s,0,0],[0,s,0],[0,0,1]])

    cu = np.mean(pts2d[:,0])
    cv = np.mean(pts2d[:,1])
    m2 = np.asarray([[1,0,-cu],[0,1,-cv],[0,0,1]])

    return np.dot(m1,m2)
    pass


def normalize_points(pts2d, t):
    """Normalizes 2D points.

    Args:
        pts2d (numpy.array): corresponding 2D (u, v) points of shape (N, 2). Where N is the number of points.
        t (numpy.array): transformation matrix T of shape (3, 3).

    Returns:
        numpy.array: normalized points (N, 2) array.
    """
    i=0
    newPts2d = np.copy(pts2d)
    for point in pts2d:
        p1 = np.append(point,1)
        newPoint = np.dot(t,p1)
        newPts2d[i] = [newPoint[0],newPoint[1]]
        i+=1

    return newPts2d
    pass
