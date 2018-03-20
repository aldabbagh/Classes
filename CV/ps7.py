"""Problem Set 7: Particle Filter Tracking."""

import numpy as np
import cv2
import random
from time import time
import os

# I/O directories
input_dir = "input"
output_dir = "output"


# Assignment code
class ParticleFilter(object):
    """A particle filter tracker, encapsulating state, initialization and update methods. Refer to the method
    run_particle_filter( ) in experiment.py to understand how this class and methods work.
    """

    def __init__(self, frame, template, **kwargs):
        """Initializes the particle filter object.

        The main components of your particle filter should at least be:
        - self.particles (numpy.array): Here you will store your particles. This should be a N x 2 array where
                                        N = self.num_particles. This component is used by the autograder so make sure
                                        you define it appropriately.
        - self.weights (numpy.array): Array of N weights, one for each particle.
                                      Hint: initialize them with a uniform normalized distribution (equal weight for
                                      each one). Required by the autograder.
        - self.template (numpy.array): Cropped section of the first video frame that will be used as the template to
                                       track.
        - self.frame (numpy.array): Current video frame from cv2.VideoCapture().

        Args:
            frame (numpy.array): color BGR uint8 image of initial video frame, values in [0, 255].
            template (numpy.array): color BGR uint8 image of patch to track, values in [0, 255].
            kwargs: keyword arguments needed by particle filter model, including:
                    - num_particles (int): number of particles.
                    - sigma_mse (float): sigma value used in the similarity measure.
                    - sigma_dyn (float): sigma value that can be used when adding gaussian noise to u and v.
                    - template_rect (dict): Template coordinates with x, y, width, and height values.
        """

        self.num_particles = kwargs.get('num_particles')  # required by the autograder
        self.sigma_exp = kwargs.get('sigma_exp')  # required by the autograder
        self.sigma_dyn = kwargs.get('sigma_dyn')  # required by the autograder
        self.template_rect = kwargs.get('template_coords')  # required by the autograder
        # If you want to add more parameters, make sure you set a default value so that
        # your test doesn't fail the autograder because of an unknown or None value.
        #
        # The way to do it is:
        # self.some_parameter_name = kwargs.get('parameter_name', default_value)

        self.template = template
        template_y = template.shape[0]
        template_x = template.shape[1]
        self.frame = frame
        dim1 = frame.shape[0]
        dim2 = frame.shape[1]

        self.particles = self.createDistribution(self.num_particles, frame, template)  # Todo: Initialize your particles array. Read the docstring.
        self.weights = np.ones(self.num_particles)/self.num_particles  # Todo: Initialize your weights array. Read the docstring.
        # Initialize any other components you may need when designing your filter.

    def myArange(self, particlesPerColumn,index,tempRow,tempColumn,imgRow,imgColumn):
        row_scale = imgRow - tempRow
        column_scale = imgColumn - tempColumn
        row_idx = (index/particlesPerColumn)/float(particlesPerColumn)
        column_idx = (index%particlesPerColumn)/float(particlesPerColumn)
        output = [row_idx*row_scale+int(tempRow/2),column_idx*column_scale+int(tempColumn/2)]
        return output

    def createDistribution(self,numparticles, image, template):
        points = []
        tempRow,tempColumn,_ = template.shape
        imgRow,imgColumn,_ = image.shape
        particlesPerColumn = int(np.floor(np.sqrt(numparticles)))
        particleIndices = range(numparticles)
        for index in particleIndices:
            temp = self.myArange(particlesPerColumn,index,tempRow,tempColumn,imgRow,imgColumn)
            points.append(temp)
        return np.asarray(points)
        pass

    def get_particles(self):
        """Returns the current particles state.

        This method is used by the autograder. Do not modify this function.

        Returns:
            numpy.array: particles data structure.

        """
        return self.particles

    def get_weights(self):
        """Returns the current particle filter's weights.

        This method is used by the autograder. Do not modify this function.

        Returns:
            numpy.array: weights data structure.
        """

        return self.weights

    def process(self, frame):
        """Processes a video frame (image) and updates the filter's state.

        This process is also inherited from ParticleFilter. Depending on your implementation, you may comment out this
        function and use helper methods that implement the "Appearance Model" procedure.

        Args:
            frame (numpy.array): color BGR uint8 image of current video frame, values in [0, 255].

        Returns:
            None.
        """
        def MSE(particle, template, img,count):
            image = np.copy(img)
            m,n = template.shape
            up,vp = particle
            m_mod = m%2
            n_mod = n%2

            xstart = int(up-((m-1)/2))
            xend = int(up+((m-1)/2))+1

            if (m%2)==0:
                xstart = int(up-m/2)
                xend = int(up+m/2)

            ystart = int(vp-((n-1)/2))
            yend = int(vp+((n-1)/2))+1

            if (n%2)<1:
                ystart = int(vp-n/2)
                yend = int(vp+n/2)

            image_portion = image[xstart:xend,ystart:yend]

            diff = np.mean((image_portion-template)**2)

            return diff

        def dynamics(particles,sigma,template):
            dim1,dim2,_ = self.frame.shape
            m,n = template.shape
            new_particles= np.copy(particles)
            us = new_particles[:,0] + np.random.normal(0,sigma, len(new_particles[:,0])) #TODO: ensure does not fall off frame
            us[us<int(m/2)]= int(m/2)+1
            us[us>dim1-int(m/2)]= dim1-int(m/2)-1

            vs = new_particles[:,1] + np.random.normal(0,sigma, len(new_particles[:,1])) #TODO: ensure does not fall off frame
            vs[vs<int(n/2)] = int(n/2)+1
            vs[vs>dim2-int(n/2)] = dim2-int(n/2)-1

            new_particles[:,0] = np.round(us).astype(np.int)
            new_particles[:,1] = np.round(vs).astype(np.int)
            return new_particles
            pass

        #TODO: sampling function
        #gotten from cs8803: AI for robotics
        def sampleParticles(particles,weights,N):
            p = np.empty((N,2))
            index = int(random.random() * N)
            beta = 0.0
            mw = np.max(weights)
            for i in range(N):
                beta += random.random() * 2.0 * mw
                while beta > weights[index]:
                    beta -= weights[index]
                    index = (index + 1) % N
                p[i] = particles[index]
            return p

        image = 0.12*frame[:,:,0] + 0.58*frame[:,:,1] +0.3*frame[:,:,2]
        particles = self.particles
        weights = self.weights
        template = 0.12*self.template[:,:,0] + 0.58*self.template[:,:,1] +0.3*self.template[:,:,2]
        sigma_exp = self.sigma_exp
        sigma_dyn = self.sigma_dyn

        sampled_particles = sampleParticles(particles,weights,len(particles))

        updated_weights = np.copy(weights)
        for i in range(len(sampled_particles)):
            updated_weights[i] = MSE(sampled_particles[i], template, image,i)
        updated_weights = np.exp(-updated_weights/(2*(sigma_exp**2)))
        updated_weights = updated_weights/sum(updated_weights)

        new_particles = dynamics(sampled_particles,sigma_dyn,template)
        self.particles = new_particles

        #self.particles = sampled_particles

        self.weights = updated_weights
        pass

    def render(self, frame_in):
        """Visualizes current particle filter state.

        This method may not be called for all frames, so don't do any model updates here!
        These steps will calculate the weighted mean. The resulting values should represent the
        tracking window center point.

        In order to visualize the tracker's behavior you will need to overlay each successive
        frame with the following elements:

        - Every particle's (u, v) location in the distribution should be plotted by drawing a
          colored dot point on the image. Remember that this should be the center of the window,
          not the corner.
        - Draw the rectangle of the tracking window associated with the Bayesian estimate for
          the current location which is simply the weighted mean of the (u, v) of the particles.
        - Finally we need to get some sense of the standard deviation or spread of the distribution.
          First, find the distance of every particle to the weighted mean. Next, take the weighted
          sum of these distances and plot a circle centered at the weighted mean with this radius.

        This function should work for all particle filters in this problem set.

        Args:
            frame_in (numpy.array): copy of frame to render the state of the particle filter.
        """

        u_weighted_mean = 0
        v_weighted_mean = 0

        for i in range(self.num_particles):
            u_weighted_mean += self.particles[i, 0] * self.weights[i]
            v_weighted_mean += self.particles[i, 1] * self.weights[i]

        # Complete the rest of the code as instructed.
        template = self.template
        dx = int(np.floor(template.shape[0]/2))
        dy = int(np.floor(template.shape[1]/2))
        cv2.rectangle(frame_in, (int(v_weighted_mean-dy),int(u_weighted_mean+dx)), (int(v_weighted_mean+dy),int(u_weighted_mean-dx)), (255,0,0), 2)

        radius = 0
        for i in range(self.num_particles):
            particleU = int(self.particles[i,0])
            particleV = int(self.particles[i,1])
            radius += self.weights[i]*((particleU-u_weighted_mean)**2+(particleV-v_weighted_mean)**2)**0.5
            cv2.circle(frame_in,(particleV,particleU),2,(0,0,255))

        cv2.circle(frame_in,(int(v_weighted_mean),int(u_weighted_mean)),int(np.round(radius)),(0,255,0))
        pass


class AppearanceModelPF(ParticleFilter):
    """A variation of particle filter tracker that updates its appearance model over time."""

    def __init__(self, frame, template, **kwargs):
        """Initializes the appearance model particle filter object (parameters are the same as ParticleFilter).

        The documentation for this class is the same as the ParticleFilter above. There is one element that is added
        called alpha which is explained in the problem set documentation. By calling super(...) all the elements used
        in ParticleFilter will be inherited so you do not have to declare them again.
        """

        super(AppearanceModelPF, self).__init__(frame, template, **kwargs)  # call base class constructor

        self.alpha = kwargs.get('alpha')  # required by the autograder
        # If you want to add more parameters, make sure you set a default value so that
        # your test doesn't fail the autograder because of an unknown or None value.
        #
        # The way to do it is:
        # self.some_parameter_name = kwargs.get('parameter_name', default_value)

    def process(self, frame):
        """Processes a video frame (image) and updates the filter's state.

        This process is also inherited from ParticleFilter. Depending on your implementation, you may comment out this
        function and use helper methods that implement the "Appearance Model" procedure.

        Args:
            frame (numpy.array): color BGR uint8 image of current video frame, values in [0, 255].

        Returns:
            None.
        """
        def MSE(particle, template, img,count):
            image = np.copy(img)
            m,n = template.shape
            up,vp = particle
            m_mod = m%2
            n_mod = n%2

            xstart = int(up-((m-1)/2))
            xend = int(up+((m-1)/2))+1

            if (m%2)==0:
                xstart = int(up-m/2)
                xend = int(up+m/2)

            ystart = int(vp-((n-1)/2))
            yend = int(vp+((n-1)/2))+1

            if (n%2)<1:
                ystart = int(vp-n/2)
                yend = int(vp+n/2)

            image_portion = image[xstart:xend,ystart:yend]
            if image_portion.shape != template.shape:
                return 100000
            diff = np.mean((image_portion-template)**2)

            return diff

        def dynamics(particles,sigma,template):
            dim1,dim2,_ = self.frame.shape
            m,n = template.shape
            new_particles= np.copy(particles)
            us = new_particles[:,0] + np.random.normal(0,sigma, len(new_particles[:,0])) #TODO: ensure does not fall off frame
            us[us<int(m/2)]= int(m/2)+1
            us[us>dim1-int(m/2)]= dim1-int(m/2)-1

            vs = new_particles[:,1] + np.random.normal(0,sigma, len(new_particles[:,1])) #TODO: ensure does not fall off frame
            vs[vs<int(n/2)] = int(n/2)+1
            vs[vs>dim2-int(n/2)] = dim2-int(n/2)-1

            new_particles[:,0] = np.round(us).astype(np.int)
            new_particles[:,1] = np.round(vs).astype(np.int)
            return new_particles
            pass

        #TODO: sampling function
        #gotten from cs8803: AI for robotics
        def sampleParticles(particles,weights,N):
            p = np.empty((N,2))
            index = int(random.random() * N)
            beta = 0.0
            mw = np.max(weights)
            for i in range(N):
                beta += random.random() * 2.0 * mw
                while beta > weights[index]:
                    beta -= weights[index]
                    index = (index + 1) % N
                p[i] = particles[index]
            return p

        kernel = np.ones((5,5),np.float32)/25
        frame = cv2.filter2D(frame,-1,kernel)

        image = 0.12*frame[:,:,0] + 0.58*frame[:,:,1] +0.3*frame[:,:,2]
        #image = 0.0*frame[:,:,0] + 1*frame[:,:,1] +0.0*frame[:,:,2]

        particles = self.particles
        weights = self.weights
        template = 0.12*self.template[:,:,0] + 0.58*self.template[:,:,1] +0.3*self.template[:,:,2]
        #template = 0.0*self.template[:,:,0] + 1*self.template[:,:,1] +0*self.template[:,:,2]

        sigma_exp = self.sigma_exp
        sigma_dyn = self.sigma_dyn

        sampled_particles = sampleParticles(particles,weights,len(particles))

        updated_weights = np.copy(weights)
        for i in range(len(sampled_particles)):
            updated_weights[i] = MSE(sampled_particles[i], template, image,i)
        updated_weights = np.exp(-updated_weights/(2*(sigma_exp**2)))
        updated_weights = updated_weights/sum(updated_weights)

        new_particles = dynamics(sampled_particles,sigma_dyn,template)
        self.particles = new_particles

        #self.particles = sampled_particles

        self.weights = updated_weights
        best_match = np.argmax(self.weights)
        best_particle = sampled_particles[best_match]
        m,n,_ = self.template.shape
        up,vp = best_particle

        xstart = int(up-((m-1)/2))
        xend = int(up+((m-1)/2))+1

        if (m%2)==0:
            xstart = int(up-m/2)
            xend = int(up+m/2)

        ystart = int(vp-((n-1)/2))
        yend = int(vp+((n-1)/2))+1

        if (n%2)<1:
            ystart = int(vp-n/2)
            yend = int(vp+n/2)

        image_portion = self.frame[xstart:xend,ystart:yend,:]
        self.template = self.alpha*image_portion+(1-self.alpha)*self.template

        pass


class MeanShiftLitePF(ParticleFilter):
    """A variation of particle filter tracker that uses the color distribution of the patch."""

    def __init__(self, frame, template, **kwargs):
        """Initializes the Mean Shift Lite particle filter object (parameters are the same as ParticleFilter).

        The documentation for this class is the same as the ParticleFilter above. There is one element that is added
        called alpha which is explained in the problem set documentation. By calling super(...) all the elements used
        in ParticleFilter will be inherited so you don't have to declare them again."""

        super(MeanShiftLitePF, self).__init__(frame, template, **kwargs)  # call base class constructor
        self.num_bins = kwargs.get('hist_bins_num', 8)  # required by the autograder
        # If you want to add more parameters, make sure you set a default value so that
        # your test doesn't fail the autograder because of an unknown or None value.
        #
        # The way to do it is:
        # self.some_parameter_name = kwargs.get('parameter_name', default_value)

    def process(self, frame):
        """Processes a video frame (image) and updates the filter's state.

        This process is also inherited from ParticleFilter. Depending on your implementation, you may comment out this
        function and use helper methods that implement the "Appearance Model" procedure.

        Args:
            frame (numpy.array): color BGR uint8 image of current video frame, values in [0, 255].

        Returns:
            None.
        """
        def getNewVector(vect):
            denom = np.linalg.norm(vect)
            if denom !=0:
                return vect/denom
            return vect

        def MSE(particle, template, img,count):
            image = np.copy(img)

            image_B = image[:,:,0]
            image_G = image[:,:,1]
            image_R = image[:,:,2]

            template_B = template[:,:,0]
            template_G = template[:,:,1]
            template_R = template[:,:,2]

            m,n = template_B.shape
            up,vp = particle
            m_mod = m%2
            n_mod = n%2

            xstart = int(up-((m-1)/2))
            xend = int(up+((m-1)/2))+1

            if (m%2)==0:
                xstart = int(up-m/2)
                xend = int(up+m/2)

            ystart = int(vp-((n-1)/2))
            yend = int(vp+((n-1)/2))+1

            if (n%2)<1:
                ystart = int(vp-n/2)
                yend = int(vp+n/2)

            image_portion_B = image_B[xstart:xend,ystart:yend]
            image_portion_G = image_G[xstart:xend,ystart:yend]
            image_portion_R = image_R[xstart:xend,ystart:yend]
            image_portion = image[xstart:xend,ystart:yend]

            h_img_B = cv2.calcHist([image_portion.astype('float32')],
                                   channels=[0],
                                   mask=None,
                                   histSize=[self.num_bins],
                                   ranges=[0,self.num_bins]).tolist()

            h_img_G = cv2.calcHist([image_portion.astype('float32')],
                                   channels=[1],
                                   mask=None,
                                   histSize=[self.num_bins],
                                   ranges=[0,self.num_bins]).tolist()
            h_img_R = cv2.calcHist([image_portion.astype('float32')],
                                   channels=[2],
                                   mask=None,
                                   histSize=[self.num_bins],
                                   ranges=[0,self.num_bins]).tolist()
            h_img_B_norm = getNewVector(np.asarray(h_img_B))
            h_img_G_norm = getNewVector(np.asarray(h_img_G))
            h_img_R_norm = getNewVector(np.asarray(h_img_R))
            h_image = getNewVector(np.concatenate((h_img_B_norm,h_img_G_norm,h_img_R_norm),axis=0))

            h_temp_B = cv2.calcHist([template.astype('float32')],
                                   channels=[0],
                                   mask=None,
                                   histSize=[self.num_bins],
                                   ranges=[0,self.num_bins]).tolist()

            h_temp_G = cv2.calcHist([template.astype('float32')],
                                   channels=[1],
                                   mask=None,
                                   histSize=[self.num_bins],
                                   ranges=[0,self.num_bins]).tolist()
            h_temp_R = cv2.calcHist([template.astype('float32')],
                                   channels=[2],
                                   mask=None,
                                   histSize=[self.num_bins],
                                   ranges=[0,self.num_bins]).tolist()

            h_temp_B_norm = getNewVector(np.asarray(h_temp_B))
            h_temp_G_norm = getNewVector(np.asarray(h_temp_G))
            h_temp_R_norm = getNewVector(np.asarray(h_temp_R))
            h_temp = getNewVector(np.concatenate((h_temp_B_norm,h_temp_G_norm,h_temp_R_norm),axis=0))

            #h_temp = np.asarray(h_temp_B+h_img_G+h_temp_R)

            if image_portion_B.shape != template_B.shape:
                print "ERROR !"
                return 100000
            denom = h_image+h_temp
            denom[denom==0] = np.inf
            diff = 0.5*np.sum((h_image-h_temp)**2/(denom))

            return diff

        def dynamics(particles,sigma,template):
            dim1,dim2,_ = self.frame.shape
            m,n = template[:,:,0].shape
            new_particles= np.copy(particles)
            us = new_particles[:,0] + np.random.normal(0,sigma, len(new_particles[:,0])) #TODO: ensure does not fall off frame
            us[us<int(m/2)]= int(m/2)+1
            us[us>dim1-int(m/2)]= dim1-int(m/2)-1

            vs = new_particles[:,1] + np.random.normal(0,sigma, len(new_particles[:,1])) #TODO: ensure does not fall off frame
            vs[vs<int(n/2)] = int(n/2)+1
            vs[vs>dim2-int(n/2)] = dim2-int(n/2)-1

            new_particles[:,0] = np.round(us).astype(np.int)
            new_particles[:,1] = np.round(vs).astype(np.int)
            return new_particles
            pass

        #TODO: sampling function
        #gotten from cs8803: AI for robotics
        def sampleParticles(particles,weights,N):
            p = np.empty((N,2))
            index = int(random.random() * N)
            beta = 0.0
            mw = np.max(weights)
            for i in range(N):
                beta += random.random() * 2.0 * mw
                while beta > weights[index]:
                    beta -= weights[index]
                    index = (index + 1) % N
                p[i] = particles[index]
            return p

        kernel = np.ones((5,5),np.float32)/25
        frame = cv2.filter2D(frame,-1,kernel)

        image = frame

        particles = self.particles
        weights = self.weights
        template = self.template

        sigma_exp = self.sigma_exp
        sigma_dyn = self.sigma_dyn

        sampled_particles = sampleParticles(particles,weights,len(particles))

        updated_weights = np.copy(weights)
        for i in range(len(sampled_particles)):
            updated_weights[i] = MSE(sampled_particles[i], template, image,i)
        updated_weights = np.exp(-updated_weights/(2*(sigma_exp**2)))
        updated_weights = updated_weights/sum(updated_weights)

        new_particles = dynamics(sampled_particles,sigma_dyn,template)
        self.particles = new_particles

        #self.particles = sampled_particles

        self.weights = updated_weights

        pass


class MDParticleFilter(ParticleFilter):
    """A variation of particle filter tracker that incorporates more dynamics."""

    def __init__(self, frame, template, **kwargs):
        """Initializes MD particle filter object (parameters same as ParticleFilter).

        The documentation for this class is the same as the ParticleFilter above.
        By calling super(...) all the elements used in ParticleFilter will be inherited so you
        don't have to declare them again.
        """

        super(MDParticleFilter, self).__init__(frame, template, **kwargs)  # call base class constructor
        # If you want to add more parameters, make sure you set a default value so that
        # your test doesn't fail the autograder because of an unknown or None value.
        #
        # The way to do it is:
        # self.some_parameter_name = kwargs.get('parameter_name', default_value)

    def process(self, frame):
        """Processes a video frame (image) and updates the filter's state.

        This process is also inherited from ParticleFilter. Depending on your implementation, you may comment out this
        function and use helper methods that implement the "More Dynamics" procedure.

        Args:
            frame (numpy.array): color BGR uint8 image of current video frame, values in [0, 255].

        Returns:
            None.
        """
        pass
