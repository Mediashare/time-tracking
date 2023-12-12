# Marathon
## Introduction
Marathon is a command-line tool written in PHP and Symfony that empowers you to efficiently **manage time for your projects**. It provides a comprehensive solution for maintaining a track record of project-related activities through commit history.
### Features
- **Commit:** Easily associate time entries with project commits to maintain a detailed history of actions taken during the development process.
- **Efficient Time Management:** Streamline your workflow by seamlessly integrating time into your version control system. Focus on development while keeping an accurate record of time spent on each task.
- **Symfony Framework:** Built on the robust Symfony framework, ensuring reliability, scalability, and ease of maintenance for your time management needs.
## Installation
### Composer
```bash
composer global require mediashre/marathon
```
### Binary
```bash
curl --output marathon https://github.com/Mediashare/marathon/raw/master/marathon
chmod 755 marathon
sudo cp marathon /usr/local/bin/marathon
```
## Usage
Here are some examples of how to use Marathon:
- To track the time you spend on a project, you can create a task for each phase of the project.
- To track the time you spend on a recurring task, you can create a task with a start date and an end date.
- To track the time you spend on a task with a client or vendor, you can add this information to the task.

```bash
  marathon timer:list                        Displaying the timer list
  marathon timer:start                       Starting timer step selected
  marathon timer:stop                        Stoping timer step selected
  marathon timer:status                      Displaying status of timer selected
  marathon timer:archive                     Archiving the timer selected
  marathon timer:remove                      Removing the timer selected

  marathon timer:commit <?COMMIT_MESSAGE>    Creating new commit into timer selected
  marathon timer:commit:edit <?COMMIT_ID>    Editing the commit from timer selected
  marathon timer:commit:remove <?COMMIT_ID>  Remove commit
  
  marathon timer:gitignore                   Adding .marathon rule into .gitgnore
  marathon timer:upgrade                     Upgrading to latest version of Marathon
```
## Contributing
Marathon is an open-source project. You can contribute to the project by submitting bug fixes, improvements, or new features.

To contribute to the project, you can follow these instructions:
- Clone the marathon GitHub repository
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
Marathon is a simple and effective tool that can help you better manage your time. If you are looking for a free and open-source time tracker, Marathon is a good option.