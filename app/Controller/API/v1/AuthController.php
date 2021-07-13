<?php

class AuthController extends APIController
{
    /**
     * User signUp
     */
    public function signUp()
    {
        // get json body
        $data = $this->getJsonBody();

        // check username and password fields
        if ( ! isset($data['username']) || ! isset($data['password'])){
            $this->errorResponse('Username and password are required fields.', 422);
        }

        // get username
       $username = $data['username'];
        // apply hash to password
       $password = hash('sha256', $data['password']);

       // check exists user
        if ($this->redis->hget("users",$username)){
            $this->errorResponse('The user already registered.', 422);
        }

        // get next user id
        $userId = $this->redis->incr("nextUserId");

        // add user to h set
        $this->redis->hset("users",$username,$userId);

        // add user details to h set
        $this->redis->hmset("user:".$userId, [
            'username' => $username,
            'password' => $password
        ]);

        // return success response with user data
        $this->successResponse([
            'id' => $userId,
            'username' => $username,
            'password' => $password
        ]);
    }

    /**
     * User SignIn
     */
    public function signIn()
    {
        // get json body
        $data = $this->getJsonBody();

        // get username
        $username = $data['username'];
        // apply hash to password
        $password = hash('sha256', $data['password']);

        // get user id
        $userId = $this->redis->hget('users', $username);

        // if user id not exists, return error
        if ( ! $userId ){
            $this->errorResponse('Wrong username or password. Try again', 422);
        }

        // get user password
        $userPassword = $this->redis->hget('user:'.$userId,"password");

        // check request pass and user password is equal
        if ($userPassword != $password){
            $this->errorResponse('Wrong password. Try again', 422);
        }

        // if there is any error, return success response
        $this->successResponse([
            'id' => $userId,
            'username' => $username
        ]);
    }
}