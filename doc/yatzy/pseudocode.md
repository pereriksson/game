# Pseudocode for a Yatzy game

## Gameflow

```
// Player wishes to start the game
If an instance of the game doesn't exist
    Create a new instance of the game
    Add a player for the human
    Add a player for the computer
    Create a new round

    If the user wishes to throw the dices
        If the number of throws is 3:
            Set number of throws to 1
        If user has requested to keep dices
            Foreach dices as dice:
                Set dice as kept
        Throw the dice

// Reset the score0
The user wishes to reset the score
    Create a new round
    
    // Reset the DiceHand
    For each diceHand's dice:
        Set the value to zero
        Set the kept flag to false
    Empty the list of rounds for the game
    Set the current throw round to 1
    Empty the list of score cards for the game
    
    For each player:
        Create new score cards for the player
```