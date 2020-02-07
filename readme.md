### Restsweeper

Restsweeper is a version of the famous [minesweeper](https://en.wikipedia.org/wiki/Minesweeper_(video_game)) game written in php that is totally REST. Nothing, no session nothing, is kept by the server and the game happens
totally sending all the state information through http parameters (GET and POST).

As a proof-of-concept it has been designed and developed with simplicity in mind so the size of the minefield is not programmable nor its difficulty, i.e. the number of hidden mines.

The minefielf is a square featuring 8 rows and 8 columns and 30% of its 64 cells is filled randomly with mines.

Besides the source code, the executable code can be seen in action here [Restsweeper](www.fjmessgeraete.ch/xxxxx/restsweeper.php)

## Rules and minefield

## Implementation details.

When the main php file named restsweeper.php is accessed, an new minefield is generated and returned to the client.
This dynamic html file basically contains a table that shows the current status of the game, and each cell features an url that has the following commands.

|parameter|type|description|
|:--------|:---|:----------|
|field|hex string| 64 bits hex coded that represent a bitmap where each bit set is a mine |
|flags|hex string| 64 bits hex coded that represent a bitmap where each bit set is a flagged cell |
|uncovered|hex string| 64 bits hex coded that represent a bitmap where each bit set is an uncovered cell |
|Action|string| 3 character string having the format XYS. The coorinates requesting the action has (X,Y) coordinates, each of which is a hex digit from 0 to 7 and S is the state that may be C (for clicked) or F (for flagged). |
|game| hex string | 64 bit unsigned (8 bytes hex coded) that represent a game identifier. The most significant 32 bits contain a random number generated when the game starts, the following 4 bytes represent a hash function of the same unsigned. If it matches, the transaction is OK |

### Hex coded bitmap.

The state of the minefield is encoded by means of three bitmaps that are encoded in hex.

Suppose the bitmap is as follows

| |0|1|2|3|4|5|6|7| Hex |
|:-|:-|:-|:-|:-|:-|:-|:-|:-|-:|
|0|0|0|0|1|0|0|0|0|10|
|1|0|1|1|0|0|0|0|0|B0|
|2|0|0|0|1|0|0|0|0|10|
|3|0|0|0|1|0|0|0|0|10|
|4|0|0|0|0|0|0|0|0|00|
|5|0|0|1|0|0|1|0|0|24|
|6|0|0|0|0|1|1|1|0|0E|
|7|0|0|0|0|1|0|0|0|08|

Then it will be represented with the 16 hex characters string "10B0101000240E08"