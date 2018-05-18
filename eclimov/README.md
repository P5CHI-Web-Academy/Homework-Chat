# Homework-Chat
**Features & specification**
* Symfony 4
* Basic user registration. \
Each user has his own access token, through which he can access the chat
* Custom avatars & default avatars
* Basic profile settings.\
User is able to edit his name, email and avatar
* Real-time dialogues' update.\
Just like in any modern chat
* Messages' validation
* "New message(s)" icon near contact
* "Last message" text(if any) near contact
* Read/unread messages logic.\
Unread messages are shown in notifications, until corresponding dialogue is opened and the messages are read
* "Online" status.\
User is considered "Online" if the last message he sent was (<20min) ago
* More

**Init instructions**
1. Proceed to project folder  
`cd eclimov`
2. Install dependencies  
`npm install`  
`composer install`
3. Start container  
`docker-compose up -d`
4. Access PHP container's console  
`docker-compose exec php-fpm bash`
5. Create DB schema(tables, relations, etc.)  
`bin/console doctrine:schema:create`
6. Set write permissions to public uploads folder  
`chmod -R 777 public/uploads`
7. Open `http://localhost`
