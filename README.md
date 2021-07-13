## Description
Redis based Game API with pure PHP.
## Installation

```bash
git clone https://github.com/koparal/game-api.git
```

## Composer

```bash
composer install
```


## Set Configurations

```bash
# Default config

const REDIS_HOST = '127.0.0.1';
const REDIS_PORT = 6379;
const REDIS_PERSISTENT = 0;
```

## API

### SignUp

    Endpoint : /api/v1/user/signup
    Method : POST
    Content-Type : application/json

    Body : {
      "username": "user",
      "password": "password" 
    }

    Response : {
        "status": "success",
        "timestamp": "0000-00-00 00:00:00",
        "result": {
            "id": 1,
            "username": "user",
            "password": "password"
        }
    }
      

### SignIn


    Endpoint : /api/v1/user/signin
    Method : POST
    Content-Type : application/json

    Body : {
      "username": "user",
      "password": "password" 
    }

    Response : {
        "status": "success",
        "timestamp": "0000-00-00 00:00:00",
        "result": {
            "id": "1",
            "username": "user"
        }
    }


      
### Endgame
  
    Endpoint : /api/v1/endgame
    Method : POST
    Content-Type : application/json

    Body : {
        "Players" : [
            {
                "id": 1,
                "score": 10
            },
            {
                "id": 2,
                "score": 6
            }
        ]
    }

    Response : {
    "status": "success",
    "timestamp": "0000-00-00 00:00:00",
    "result": [
            {
                "rank": 1,
                "userId": "user1",
                "score": 10
            },
            {
                "rank": 2,
                "userId": "user2",
                "score": 6
            }
        ]
    }


### Leaderboard

    Endpoint : /api/v1/leaderboard
    Method : GET
    Content-Type : application/json

    Response : {
    "status": "success",
    "timestamp": "0000-00-00 00:00:00",
    "result": [
        {
            "rank": 1,
            "id": 4,
            "username": "user4",
            "score": "150"
        },
        {
            "rank": 3,
            "id": 1,
            "username": "user1",
            "score": "110"
        },
        {
            "rank": 4,
            "id": 2,
            "username": "user2",
            "score": "93"
        }
    ]
    }
       
