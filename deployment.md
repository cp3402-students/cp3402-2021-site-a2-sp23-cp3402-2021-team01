# Project Management
To facilitate our communication and task management, we have used Slack Channel, GitHub repository and Trello board.

* [GitHub Repository](https://github.com/cp3402-students/cp3402-2021-site-a2-sp23-cp3402-2021-team01)<br/>
It was used to collaboratively work on the project and to keep track of the changes made by everyone to the prject files.
* [Trello Board](https://trello.com/b/punpxvWS/a2-sp23-cp3402-2021-team01)<br/>
It was used to ideate, plan, and manage the team work together in a collaborative, productive, and organized way.
* [Slack Channel](https://itatjcub.slack.com/archives/C02FUHAAR7G)<br/>
We integrated the GitHub repository and Trello board with the Slack channel to get automatic notifications and updates in the Slack channel from the changes published in Trello board and GitHub repository so that every team member could see what everyone else in the team was working on.

# Environments
We established a good workflow with multiple environments, including Local development environment, the staging server  and the production server.

* Local development environment:<br/>
We have used XAMPP including all of the needed applications (Apache, MySQL, PHP) as our local development environment. We have used it to create our own custom theme. 
* The staging server:<br/>
Testing our own custom theme in a live environment so as not to break the real website on the production server, creating and editing content.
performing site management.
* The production server:<br/>
A server used to host website content for deployment to a live environment with the latest and tested theme and site content.

# Website Hosting
Both our staging and production websites are hosted on AWS as it is the industry standard.

# Deployment
Local Development Environment- XAMPP
Staging Server- [http://54.206.6.44/](http://54.206.6.44/)
Production Server- [http://54.253.76.127/](http://54.253.76.127/)

# Version Control
Version control has been done with GitHub. It is the practice of tracking, managing and comparing the old and the new version of the code that has been commited by each team member to the repository.

# Editing our own custom theme
If you wish to edit our own custom theme- [nicetheme](https://github.com/cp3402-students/cp3402-2021-site-a2-sp23-cp3402-2021-team01/tree/main/wp-content/themes/nicetheme), first of all, ensure that you have XAMPP installed on the local development environment, Apache and MySQL modules running and WordPress installed. Editing can be done once our custom theme folder is downloaded from wp-content folder and installed in your WordPress themes. Install PHPStorm for editing as it knows SASS and can create a File Watcher to watch for changes and automatically output CSS.

# Editing our website content
[All-In-One WP Migration](https://wordpress.org/plugins/all-in-one-wp-migration/)<br/>
We used All-In-One WP Migration to export the changes made in local environemnt development and staging server to the production server.
