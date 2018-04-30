Simple chat application
------

#### Installation

1. Install composer dependencies
2. Install node modules
3. Run 'gulp js' to build public files (or 'gulp dev')
4. Launch WebSocket server

This project makes use of websocket server, for a linux docker environment run (leave the process running):

```bash
bash docker-run.sh
```

Otherwise (for a local web server setup) just also run web script from project root location:

```bash
php bin/ChatServer.php
```

