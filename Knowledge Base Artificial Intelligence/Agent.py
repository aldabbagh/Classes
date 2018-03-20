# Your Agent for solving Raven's Progressive Matrices. You MUST modify this file.
#
# You may also create and submit new files in addition to modifying this file.
#
# Make sure your file retains methods with the signatures:
# def __init__(self)
# def Solve(self,problem)
#
# These methods will be necessary for the project's main method to run.

# Install Pillow and uncomment this line to access image processing.
#from PIL import Image

# Install Numpy and uncomment this line to access matrix operations.
#import numpy as np

class Agent:
    # The default constructor for your Agent. Make sure to execute any
    # processing necessary before your Agent starts solving problems here.
    #
    # Do not add any variables to this signature; they will not be used by
    # main().
    def __init__(self):
        pass

    # The primary method for solving incoming Raven's Progressive Matrices.
    # For each problem, your Agent's Solve() method will be called. At the
    # conclusion of Solve(), your Agent should return a list representing its
    # confidence on each of the answers to the question: for example 
    # [.1,.1,.1,.1,.5,.1] for 6 answer problems or [.3,.2,.1,.1,0,0,.2,.1] for 8 answer problems.
    #
    # In addition to returning your answer at the end of the method, your Agent
    # may also call problem.checkAnswer(givenAnswer). The parameter
    # passed to checkAnswer should be your Agent's current guess for the
    # problem; checkAnswer will return the correct answer to the problem. This
    # allows your Agent to check its answer. Note, however, that after your
    # agent has called checkAnswer, it will *not* be able to change its answer.
    # checkAnswer is used to allow your Agent to learn from its incorrect
    # answers; however, your Agent cannot change the answer to a question it
    # has already answered.
    #
    # If your Agent calls checkAnswer during execution of Solve, the answer it
    # returns will be ignored; otherwise, the answer returned at the end of
    # Solve will be taken as your Agent's answer to this problem.
    #
    # Make sure to return your answer *as a python list* at the end of Solve().
    # Returning your answer as a string may cause your program to crash.


    def Is_reflected(self,image1,image2,direction):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def error(frame1, frame2):
            height = frame1.size[0]
            width = frame2.size[1]
            rgb = 256
            hist = ImageChops.difference(frame1, frame2).histogram()
            sq = []
            for index, differences in enumerate(hist):
                sq.append(differences*((index%rgb)**2))
            summation = sum(sq)
            error = (summation/float(height * width))**0.5
            return error

        #image1 = Image.open(image1)
        #image2 = Image.open(image2)
        if direction == 'h':
            reflected_image = ImageOps.mirror(image1)
        elif direction == 'v':
            reflected_image = ImageOps.flip(image1)

        if error(reflected_image, image2)< 28:
            return True
        else:
            return False


        return

    def Is_correlated(self,image1,image2,image3):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def error(frame1, frame2):
            height = frame1.size[0]
            width = frame2.size[1]
            rgb = 256
            hist = ImageChops.difference(frame1, frame2).histogram()
            sq = []
            for index, differences in enumerate(hist):
                sq.append(differences*((index%rgb)**2))
            summation = sum(sq)
            error = (summation/float(height * width))**0.5
            return error

        def number_of_black(image):
            im = np.array(image)
            black = 0
            for row in range(len(im)):
                for column in range(len(im[0])):
                    temp = im[row][column]
                    if sum(temp)<1020:
                        black +=1
            return black


        black1 = number_of_black(image1)
        black2 = number_of_black(image2)
        black3 = number_of_black(image3)

        error1 = error(image1,image2)
        #print "error1: ", error1
        error2 = error(image2,image3)
        #print "error2: ", error2

        if error2<=error1+10 and error2>=error1-10 and (black3>black2 and black2>black1):
            return True
        elif error2<=error1+10 and error2>=error1-10 and (black1>black2 and black2>black3):
            return True
        else:
            return False

    def Is_same(self, image1, image2):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def Jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        max = [0,0,0]
        for i in range(-6,6):
            for j in range(-6,6):
                img3 = ImageChops.offset(image2,i,j)
                err = Jaccard(image1, img3)
                if err>max[0]:
                    max[0] = err
                    max[1] = i
                    max[2] = j

        similarity = max[0]

        threshold = 0.90

        if similarity>=threshold:
            return True
        else:
            return False

    def problem_to_dict(self, problem):
        dictionary = {}
        for figureName in problem.figures:
            thisFigure = problem.figures[figureName]
            dictionary[figureName] = {}
            for objectName in thisFigure.objects:
                thisObject = thisFigure.objects[objectName]
                dictionary[figureName][objectName]={}
                for attributeName in thisObject.attributes:
                    attributeValue = thisObject.attributes[attributeName]
                    dictionary[figureName][objectName][attributeName] = attributeValue
                #dictionary[figureName][objectName] = thisObject

        return dictionary

    def normalize(self,answer):
        summation = sum(answer)
        normalized = []
        for item in answer:
            normalized.append(float(item)/float(summation))

        return normalized

    def same_number_obj(self, frame1,frame2):
        if len(frame1) == len(frame2):
            return True
        else:
            return False

    def Is_And(self, frame1, frame2, frame3, thresh):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def Jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        img1 = frame1.convert('1')
        img2 = frame2.convert('1')

        img3 = ImageChops.logical_and(img1,img2)
        img3= img3.convert('L')
        max = [0,0,0]
        for i in range(-6,6):
            for j in range(-6,6):
                err = Jaccard(frame3.convert('L'), ImageChops.offset(img3,i,j))
                if err>max[0]:
                    max[0] = err
                    max[1] = i
                    max[2] = j

        similarity = max[0]

        threshold = thresh
        #print similarity
        if similarity>=threshold:
            return [True, similarity]
        else:
            return [False, 0.0]

    def Is_Xnor(self, frame1, frame2, frame3):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def Jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        img1 = frame1.convert('1')
        img2 = frame2.convert('1')

        img3 = ImageChops.invert(ImageChops.logical_xor(img1,img2))

        img3= img3.convert('L')
        max = [0,0,0]
        for i in range(-6,6):
            for j in range(-6,6):
                err = Jaccard(frame3.convert('L'), ImageChops.offset(img3,i,j))
                if err>max[0]:
                    max[0] = err
                    max[1] = i
                    max[2] = j

        similarity = max[0]

        threshold = 0.70
        #print similarity
        if similarity>=threshold:
            return [True, similarity]
        else:
            return [False, 0.0]

    def Is_Or(self, frame1, frame2, frame3):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def Jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        img1 = frame1.convert('1')
        img2 = frame2.convert('1')

        img3 = ImageChops.logical_or(img1,img2)
        img3= img3.convert('L')
        max = [0,0,0]
        for i in range(-6,6):
            for j in range(-6,6):
                err = Jaccard(frame3.convert('L'), ImageChops.offset(img3,i,j))
                if err>max[0]:
                    max[0] = err
                    max[1] = i
                    max[2] = j

        similarity = max[0]

        threshold = 0.85

        if similarity>=threshold:
            return True
        else:
            return False

    def Is_xor(self, frame1, frame2, frame3):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def Jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        img1 = frame1.convert('1')
        img2 = frame2.convert('1')
        img3 = ImageChops.logical_xor(img1,img2)
        img3= img3.convert('L')
        similarity = Jaccard(frame3,img3)

        threshold = 0.70

        if similarity>=threshold:
            return True
        else:
            return False

    def Is_sliding_and(self, frame1, frame2, frame3):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def Jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        max = 0.0
        for i in range(0,45):
            img1 = ImageChops.offset(frame2,-i,0)
            img2 = ImageChops.offset(frame3,i,0)
            img3 = ImageChops.logical_and(img1.convert('1'),img2.convert('1'))
            img3= img3.convert('L')
            temp = Jaccard(img3, frame1)
            if max<temp:
                max = temp
            if temp> 0.75:
                break
        similarity = max

        threshold = 0.73
        #print similarity
        if similarity>=threshold:
            return [True, similarity]
        else:
            return [False, 0.0]

    def combine_three(self, frame1, frame2, frame3):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops

        img1 = frame1.convert('1')
        img2 = frame2.convert('1')
        img3 = frame3.convert('1')

        img4 = ImageChops.logical_and(img1,img2)
        img5 = ImageChops.logical_and(img4,img3)
        return img5.convert('L')

    def compare_three(self, image1, image2):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on this link: http://code.activestate.com/recipes/577630-comparing-two-images/
        def Jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        max = [0,0,0]
        for i in range(-6,6):
            for j in range(-6,6):
                img3 = ImageChops.offset(image2,i,j)
                err = Jaccard(image1, img3)
                if err>max[0]:
                    max[0] = err
                    max[1] = i
                    max[2] = j

        similarity = max[0]

        threshold = 0.90

        if similarity>=threshold:
            return True
        else:
            return False

    def Is_incremental(self, image1, image2, image3):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np
        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        #img1 = image1.convert('1').convert('L')
        #img2 = image2.convert('1').convert('L')
        #img3 = image3.convert('1').convert('L')

        img1 = image1.convert('L')
        img2 = image2.convert('L')
        img3 = image3.convert('L')

        c1 = black_count(img1)
        c2 = black_count(img2)
        c3 = black_count(img3)

        order = [c1,c2,c3]
        order.sort()

        diff1 = order[1]-order[0]
        diff2 = order[2]-order[1]
        diff3 = order[2]-order[0]


        if diff1 >=0.85*(diff2) and (diff1)<=1.15*(diff2) and order[2]>order[1] and order[1]>order[0]:
            if diff3 >= 0.85*(diff2+diff1) and (diff3)<=1.15*(diff2+diff1):
                return True

    def diag_and(self,image1,image2,image3):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        images = [[black_count(image1.convert('L')),image1],[black_count(image2.convert('L')),image2],[black_count(image3.convert('L')),image3]]
        images.sort(key=lambda x: x[0],reverse=False)

        if self.Is_And(images[0][1],images[1][1],images[2][1],0.98):
            if not self.Is_same(images[1][1],images[2][1]):
                #combined = ImageChops.logical_and(images[0][1].convert('1'),images[1][1].convert('1'))
                #if black_count(combined.convert('L')) <= black_count(images[2][1].convert('L')):
                return True

    def added_difference(self,image1,image2,image3,image4):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on Jaccard Index on Wikipedia
        def jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        diff1 = ImageChops.logical_or(ImageOps.invert(image1.convert('L')).convert('1'),image2.convert('1'))
        addition1 = ImageChops.logical_or(image1.convert('1'), ImageOps.invert(image2.convert('L')).convert('1'))

        diff2 = ImageChops.logical_or(ImageOps.invert(image3.convert('L')).convert('1'),image4.convert('1'))
        addition2 = ImageChops.logical_or(image3.convert('1'), ImageOps.invert(image4.convert('L')).convert('1'))

        similarity1 = jaccard(diff1.convert('L'), diff2.convert('L'))
        similarity2 = jaccard(addition1.convert('L'),addition2.convert('L'))

        threshold = 0.80

        if similarity1>= threshold:
            return True
        else:
            return False

    def quick_compare(self,image1,list_of_images):
        import PIL
        from PIL import Image
        from PIL import ImageOps
        from PIL import ImageChops
        import numpy as np

        def black_count(img):
            siz = 184.0*184.0
            image = np.array(img).sum().sum()
            count = siz - image/255.0
            return count

        # Error is based on Jaccard Index on Wikipedia
        def jaccard(image1, image2):
            img1 = image1.convert('1')
            img2 = image2.convert('1')
            img3 = ImageChops.logical_or(img1, img2)
            intersection = black_count(img3.convert('L'))
            img4 = ImageChops.logical_and(image1.convert('1'), image2.convert('1'))
            union = black_count(img4.convert('L'))
            return float(intersection)/union

        for image2 in list_of_images:
            if jaccard(image1.convert('L'),image2.convert('L'))>0.885:
                return False
        return True


    def three_by_three(self,problem):
        answer = 0
        #create object list
        from PIL import Image
        problem2 = self.problem_to_dict(problem)

        A = problem2['A']
        B = problem2['B']
        C = problem2['C']
        D = problem2['D']
        E = problem2['E']
        F = problem2['F']
        G = problem2['G']
        H = problem2['H']

        ans_1 = problem2['1']
        ans_2 = problem2['2']
        ans_3 = problem2['3']
        ans_4 = problem2['4']
        ans_5 = problem2['5']
        ans_6 = problem2['6']
        ans_7 = problem2['7']
        ans_8 = problem2['8']

        possible_answers = [ans_1,ans_2,ans_3,ans_4,ans_5,ans_6, ans_7, ans_8]

        figA = problem.figures['A']
        figureA = Image.open(figA.visualFilename)

        figB = problem.figures['B']
        figureB = Image.open(figB.visualFilename)

        figC = problem.figures['C']
        figureC = Image.open(figC.visualFilename)

        figD = problem.figures['D']
        figureD = Image.open(figD.visualFilename)

        figE = problem.figures['E']
        figureE = Image.open(figE.visualFilename)

        figF = problem.figures['F']
        figureF = Image.open(figF.visualFilename)

        figG = problem.figures['G']
        figureG = Image.open(figG.visualFilename)

        figH = problem.figures['H']
        figureH = Image.open(figH.visualFilename)


        fig1 = problem.figures['1']
        figure1 = Image.open(fig1.visualFilename)

        fig2 = problem.figures['2']
        figure2 = Image.open(fig2.visualFilename)

        fig3 = problem.figures['3']
        figure3 = Image.open(fig3.visualFilename)

        fig4 = problem.figures['4']
        figure4 = Image.open(fig4.visualFilename)

        fig5 = problem.figures['5']
        figure5 = Image.open(fig5.visualFilename)

        fig6 = problem.figures['6']
        figure6 = Image.open(fig6.visualFilename)

        fig7 = problem.figures['7']
        figure7 = Image.open(fig7.visualFilename)

        fig8 = problem.figures['8']
        figure8 = Image.open(fig8.visualFilename)

        possible_frames = [figure1, figure2, figure3, figure4, figure5, figure6, figure7, figure8]
        question_frames = [figureA,figureB,figureC,figureD,figureE,figureF,figureG,figureH]

        #check if logical And
        if self.Is_And(figureA, figureB, figureC,0.79)[0] and self.Is_And(figureD,figureE,figureF,0.79)[0]:
            possible = []
            for i in range(len(possible_frames)):
                temp = self.Is_And(figureG, figureH, possible_frames[i],0.79)
                if temp[0]:
                    possible.append([i,temp[1]])
            if possible:
                possible.sort(key=lambda x: x[1],reverse=True)
                index = possible[0][0]
                answer = [0 for cell in range(8)]
                answer[index] = 1
                print '1'
                return answer

        #check if logical And v2
        if self.Is_And(figureC, figureB, figureA,0.79)[0] and self.Is_And(figureE,figureF,figureD,0.79)[0]:
            possible = []
            for i in range(len(possible_frames)):
                temp = self.Is_And(figureH, possible_frames[i], figureG,0.79)
                if temp[0] and not self.Is_same(figureG,possible_frames[i]):
                    possible.append([i,temp[1]])
            if possible:
                possible.sort(key=lambda x: x[1],reverse=True)
                index = possible[0][0]
                answer = [0 for cell in range(8)]
                answer[index] = 1
                print '2'
                return answer

        #check if logical And v3
        if self.Is_And(figureA,figureC, figureB,0.79)[0] and self.Is_And(figureD,figureF,figureE,0.79)[0]:
            possible = []
            for i in range(len(possible_frames)):
                temp = self.Is_And(figureG, possible_frames[i], figureH,0.79)
                if temp[0] and not self.Is_same(figureH,possible_frames[i]):
                    possible.append([i,temp[1]])
            if possible:
                possible.sort(key=lambda x: x[1],reverse=True)
                index = possible[0][0]
                answer = [0 for cell in range(8)]
                answer[index] = 1
                print '3'
                return answer

        #check if Xnor
        if self.Is_Xnor(figureA, figureB, figureC)[0] and self.Is_Xnor(figureD,figureE,figureF)[0]:
            possible = []
            for i in range(len(possible_frames)):
                temp = self.Is_Xnor(figureG, figureH, possible_frames[i])
                if temp[0]:
                    possible.append([i,temp[1]])
            if possible:
                possible.sort(key=lambda x: x[1],reverse=True)
                index = possible[0][0]
                answer = [0 for cell in range(8)]
                answer[index] = 1
                print '4'
                return answer

        #check if sliding and
        if self.Is_sliding_and(figureA, figureB, figureC)[0] and self.Is_sliding_and(figureD,figureE,figureF)[0]:
            possible = []
            for i in range(len(possible_frames)):
                temp = self.Is_sliding_and(figureG, figureH, possible_frames[i])
                if temp[0] and not self.Is_same(figureG, possible_frames[i]):
                    possible.append([i,temp[1]])
            if possible:
                possible.sort(key=lambda x: x[1],reverse=True)
                index = possible[0][0]
                answer = [0 for cell in range(8)]
                answer[index] = 1
                print '5'
                return answer

        #check if same frame visually
        if self.Is_same(figureA, figureB) and self.Is_same(figureB, figureC) \
                and self.Is_same(figureD, figureE) and self.Is_same(figureE,figureF)\
                and self.Is_same(figureG,figureH):
            for i in range(len(possible_frames)):
                if self.Is_same(figureH,possible_frames[i]):
                    answer = [0 for cell in range(8)]
                    answer[i] = 1
                    #print 'self 1'
                    print '6'
                    return answer

        if self.Is_same(figureA, figureD) and self.Is_same(figureD,figureG) \
                and self.Is_same(figureB,figureE) and self.Is_same(figureE,figureH)\
                and self.Is_same(figureC,figureF):
            for i in range(len(possible_frames)):
                if self.Is_same(figureF,possible_frames[i]):
                    answer = [0 for cell in range(8)]
                    answer[i] = 1
                    print '7'
                    #print 'self 2'
                    return answer

        #check diagonally
        if self.Is_same(figureB, figureG) and self.Is_same(figureC, figureH):
            for i in range(len(possible_frames)):
                if self.Is_same(figureA, possible_frames[i]):
                    answer = [0 for cell in range(8)]
                    answer[i] = 1
                    print '8'
                    #print 'diag 1'
                    return answer

        #check diagonally
        if self.Is_same(figureC, figureG) and self.Is_same(figureF, figureA):
            for i in range(len(possible_frames)):
                if self.Is_same(figureD, possible_frames[i]):
                    answer = [0 for cell in range(8)]
                    answer[i] = 1
                    print '9'
                    #print 'diag 2'
                    return answer

        #check for addition_subtraction
        answer = [0 for cell in range(8)]
        for i in range(len(possible_frames)):
            if self.added_difference(figureA, figureC, figureG, possible_frames[i]) and self.added_difference(figureD, figureF, figureG, possible_frames[i]):
                if not self.Is_same(figureA, figureG) and not self.Is_same(figureD, figureG):
                    if not self.Is_same(figureC,possible_frames[i]) and not self.Is_same(figureF,possible_frames[i]):
                        answer[i] = 1
                    else:
                        answer[i] = 0
                else:
                    if self.Is_same(figureC,possible_frames[i]) or self.Is_same(figureF,possible_frames):
                        answer[i] = 1
                    else:
                        answer[i] = 0
        if max(answer)>0:
            print '9.5'
            return answer

        #check for addition_subtraction
        answer = [0 for cell in range(8)]
        for i in range(len(possible_frames)):
            if self.added_difference(figureH, figureD, possible_frames[i], figureE):
                if not self.Is_same(figureH, possible_frames[i]):
                    if not self.Is_same(figureD, figureE):
                        answer[i] = 1
                    else:
                        answer[i] = 0
                else:
                    if self.Is_same(figureD,figureE):
                        answer[i] = 1
                    else:
                        answer[i] = 0
        if max(answer)>0:
            print '9.75'
            return answer

        #check if logical or
        if self.Is_Or(figureA, figureB, figureC) and self.Is_Or(figureD,figureE,figureF):
            for i in range(len(possible_frames)):
                if self.Is_Or(figureG, figureH, possible_frames[i]):
                    answer = [0 for cell in range(8)]
                    answer[i] = 1
                    print '10'
                    #print 'logical or'
                    return answer

        #check if logical xor
        if self.Is_xor(figureA, figureB, figureC) and self.Is_xor(figureD,figureE,figureF):
            for i in range(len(possible_frames)):
                if self.Is_xor(figureG, figureH, possible_frames[i]):
                    answer = [0 for cell in range(8)]
                    answer[i] = 1
                    print '11'
                    return answer

        #check diagonal
        if self.compare_three(self.combine_three(figureA, figureH, figureF),self.combine_three(figureC, figureE, figureG)):
            answer = [0 for cell in range(8)]
            for i in range(len(possible_frames)):
                if self.compare_three(self.combine_three(figureA, figureH, figureF),self.combine_three(figureD, figureB, possible_frames[i])):
                    answer[i] = 1
            if max(answer):
                print '12'
                return self.normalize(answer)

        #check diagonal
        if self.compare_three(self.combine_three(figureA, figureB, figureC),self.combine_three(figureD, figureE, figureF)):
            answer = [0 for cell in range(8)]
            for i in range(len(possible_frames)):
                if self.compare_three(self.combine_three(figureA, figureB, figureC),self.combine_three(figureG, figureH, possible_frames[i])):
                    if self.quick_compare(possible_frames[i],question_frames):
                        answer[i] = 1
            if max(answer) and sum(answer)<5:
                print '13'
                return self.normalize(answer)

        #check incremental DIAG1
        if self.Is_incremental(figureA, figureH, figureF) and self.Is_incremental(figureG,figureE, figureC):
            answer = [0 for cell in range(8)]
            for i in range(len(possible_frames)):
                if self.Is_incremental(figureD, figureB, possible_frames[i]):
                    answer[i] = 1
            if max(answer):
                print '14'
                return self.normalize(answer)

        #naive answer
        answer = [0 for cell in range(8)]
        for i in range(len(possible_frames)):
            if self.quick_compare(possible_frames[i],question_frames):
                answer[i] = 1
        if max(answer) and sum(answer)<5:
            print 'Naive'
            return self.normalize(answer)

        #diag Add  (consider Deleting)
        if self.diag_and(figureA, figureH, figureF) and self.diag_and(figureG,figureE, figureC):
            answer = [0 for cell in range(8)]
            for i in range(len(possible_frames)):
                if self.diag_and(figureD, possible_frames[i], figureB):
                    answer[i] = 1
            if max(answer):
                print '15'
                return self.normalize(answer)

        return [1.0/8.0]*8


    def Solve(self,problem):
    
        ## code suggested by Ryan Peach @115 + slight editing
        ## feel free to use
        ## Where a is the input list
        #t = float(sum(a))
        #out = [x/t for x in a]

        #problem_type = problem.problemType #string
        #verbal_representation = problem.hasVerbal #Boolean of whether the problem has only verbal

        #figures = problem.figures #dict of frames
        problem_type = problem.problemType
        #print problem_type

        #if problem_type == "2x2" and verbal_representation:
        objects = ['a', 'b','c', 'd', 'e', 'f', 'g', 'h','i','j','k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
                   'u', 'v', 'w', 'x' ,'y','z', ]
        nums = range(0,50)
        nums2 = [str(x) for x in nums]
        objects += nums2

        #if problem.hasVerbal == False:
        #    return [0.1666,0.1666,0.1666,0.1666,0.1666,0.1666]

        if problem_type == '2x2':
            result = self.check_simple_solutions(problem)
        else:
            result = self.three_by_three(problem)
        if max(result)>=0.25:
            return result
        else:
            return [1.0/8.0]*8


