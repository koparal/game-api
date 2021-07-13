<?php

class GameController extends APIController
{
    public function endGame()
    {
        // get json body
        $data = $this->getJsonBody();

        // check fields
        if ( ! isset($data['Players'])){
            $this->errorResponse('Players field is required.', 422);
        }

        // check count of player
        if (count($data['Players']) < 2){
            $this->errorResponse('The players must be two or more.', 422);
        }

        // get next game id
        $gameId = $this->redis->incr("nextGameId");
        // get game key
        $gameKey = 'game:'.$gameId;

        // loop for players
        foreach ($data['Players'] as $player){
            // check valid inputs
            if (isset($player['score']) && isset($player['id']) &&
                is_int($player['score']) && is_int($player['id'])
            ){
                // Add the score and user data to z list
                $this->redis->zadd($gameKey, [ $player['id'] => $player['score'] ]);
            }
        }

        // get scores
        $scores = $this->redis->zrevrange($gameKey, 0, 9, WITHSCORES);

        $rank = 0;
        $userScores = [];
        $prevScore = null;

        // loop for the scores
        foreach ($scores as $userId => $score) {

            $score = (int)$score;

            // set rank by score and prev score
            if ($score !== $prevScore) {
                $rank++;
            }

            // Get username
            $userName = $this->redis->hget('user:'.$userId, 'username');

            $userScores[] = [
                'rank' => $rank,
                'userId' => $userName,
                'score' => $score
            ];

            // define user score key
            $userScoreKey = 'userScore:'.$userId;
            // get the user score
            $userScore = $this->redis->get($userScoreKey);

            // check user score
            if ( ! $userScore){
                // add score to redis
                $this->redis->set($userScoreKey, $score);
                // set user score
                $userScore = $score;
            }else{
                // add old score and new score
                $userScore += (int)$score;
                // set user score by new score
                $this->redis->set($userScoreKey, $userScore);
            }

            // add the date to leaderboard
            $this->redis->zadd('leaderboard', [ $userId => $userScore ]);

            // set prev score with score
            $prevScore = $score;
        }

        // return response
        $this->successResponse($userScores);
    }

    public function leaderBoard()
    {
        // init user rank list
        $userRank = [];
        // get leaderboard in ordered
        $leaderBoard = $this->redis->zrevrange('leaderboard', 0, 9, WITHSCORES);

        // rank counter
        $rankCounter = 1;
        // loop for leader board
        foreach ($leaderBoard as $userId => $score){

            // Get username
            $userName = $this->redis->hget('user:'.$userId, 'username');

            // add user to rank
            $userRank[] = [
                'rank' => $rankCounter,
                'id' => $userId,
                'username' => $userName,
                'score' => $score
            ];

            $rankCounter++;
        }

        // return response
        $this->successResponse($userRank);
    }
}