# PHP Assignment

Please build REST API for the Tic-Tac-Toe game https://en.wikipedia.org/wiki/Tic-tac-toe.
We provide you with Yaml file as example of basic REST API setup

* The REST API should be implemented using PHP, no data storage is required.
Try to keep slim and omit heavy framework usage. Any required libraries and components are allowed.

## Game flow

* The client (player) starts a game, makes a request to server to initiate a TicTakToe board. ( Client (player) will always use cross )
* The backend responds with the location URL of the started game.
* Client gets the board state from the URL.
* Client makes a move; move is sent back to the server.
* Backend validates the move, makes it's own move and updates the game state. The updated game state is returned in the response.
* And so on. The game is over once the computer or the player gets 3 noughts or crosses, horizontally, vertically or diagonally or there are no moves to be made.

## Tech spec

* Time spent - 5hrs.
* Framework - [Slim framework v4](http://www.slimframework.com/)
* Installation - `composer install` and configure your webserver to look at `public/index.php`
* Folders structure

    - ***public*** - entrypoint for application
    - ***src*** - main application classes: Controllers, Models, Repositories, Services
    - ***storage*** - for games progress saving
    
* Instead of saving games progress to session, it is storing to files, because we can't be sure that client uses Cookies or some another storage for saving session id.


