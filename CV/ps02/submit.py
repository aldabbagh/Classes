import argparse

from nelson.gtomscs import submit

LATE_POLICY = """Late Policy:
  \"I have read the late policy for CS6476. I understand that only my last
  commit before the late submission deadline will be accepted and that late
  penalties apply if any part of the assignment is submitted late.\"
"""

HONOR_PLEDGE = "Honor Pledge:\n\n  \"I have neither given nor received aid on this assignment.\"\n"


def require_pledges():
    print(LATE_POLICY)
    ans = raw_input("Please type 'yes' to agree and continue>")
    if ans != "yes":
        raise RuntimeError("Late policy not accepted.")

    print
    print(HONOR_PLEDGE)
    ans = raw_input("Please type 'yes' to agree and continue>")
    if ans != "yes":
        raise RuntimeError("Honor pledge not accepted")
    print


def main():
    parser = argparse.ArgumentParser(description='Submits code to the Udacity site.')
    parser.add_argument('part', choices=['ps02', 'ps02_report'])
    args = parser.parse_args()

    quiz = args.part
    course = "cs6476"

    if quiz == "ps02":
        filenames = ["ps2.py"]
    else:
        filenames = ['ps02_report.pdf', 'experiment.py']

    require_pledges()

    submit(course, quiz, filenames)

if __name__ == '__main__':
    main()
