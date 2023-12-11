# Time-Tracking
## Introduction
Time-Tracking is a command-line tool written in PHP and Symfony that empowers you to efficiently manage time tracking for your projects. It provides a comprehensive solution for maintaining a track record of project-related activities through commit history.
### Features
- Commit Tracking: Easily associate time entries with project commits to maintain a detailed history of actions taken during the development process.
- Efficient Time Management: Streamline your workflow by seamlessly integrating time tracking into your version control system. Focus on development while keeping an accurate record of time spent on each task.
- Symfony Framework: Built on the robust Symfony framework, ensuring reliability, scalability, and ease of maintenance for your time management needs.
## Installation
### Composer
```bash
composer global require mediashre/time-tracking
```
### Binary
```bash
curl --output time-tracking https://github.com/Mediashare/time-tracking/raw/master/time-tracking
chmod 755 time-tracking
sudo cp time-tracking /usr/local/bin/time-tracking
```
## Usage
Here are some examples of how to use Time-Tracking:
- To track the time you spend on a project, you can create a task for each phase of the project.
- To track the time you spend on a recurring task, you can create a task with a start date and an end date.
- To track the time you spend on a task with a client or vendor, you can add this information to the task.

```bash
  time-tracking timer:list                       Displaying the time-tracking list
  time-tracking timer:start                      Starting time-tracking step selected
  time-tracking timer:stop                       Stoping time-tracking step selected
  time-tracking timer:status                     Displaying status of time-tracking selected
  time-tracking timer:archive                    Archiving the time-tracking selected
  time-tracking timer:remove                     Removing the time-tracking selected

  time-tracking timer:commit <?COMMIT_MESSAGE>    Creating new commit into time-tracking selected
  time-tracking timer:commit:edit <?COMMIT_ID>    Editing the commit from time-tracking selected
  time-tracking timer:commit:remove <?COMMIT_ID>  Remove commit
  
  time-tracking timer:gitignore                   Adding .time-tracking rule into .gitgnore
  time-tracking timer:upgrade                     Upgrading to latest version of Time-Tracking
```
## Contributing
Time-tracking is an open-source project. You can contribute to the project by submitting bug fixes, improvements, or new features.

To contribute to the project, you can follow these instructions:
- Clone the time-tracking GitHub repository
- Create a branch for your contribution
- Make your changes
- Test your changes
- Build your bin
- Submit a pull request
- 
### Build a bin with Box
#### Box install
[Box2](https://github.com/box-project/box) used for binary generation from php project. **PHP >=8.1 is required.**
```bash
composer global require humbug/box
box
```
#### Box usage
```bash
composer dump-env dev
box compile
```
## Conclusion
Time-tracking is a simple and effective tool that can help you better manage your time. If you are looking for a free and open-source time tracker, time-tracking is a good option.