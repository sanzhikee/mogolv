

# `Task: `
Develop software that can be used to manage tournament scoring - prepare and fill tournament schedule. User can enter list of teams. Together 16 teams are participating. Teams by random are split in 2 divisions – A and B (8 teams in each). In each division teams play each against other. The best 4 teams from each division meet in Play-off. Play-off initial schedule is made by principle - best team plays against worst team. The winning team stays to play further but the losing team is out of the game. Overall winning team is the one who wins all games in play-off.  
   
Please show your best knowledge of object-oriented programming.  
   
In order not to enter the tournament results by hand please use auto generation - by pressing a button generate Division A results, then Division B results, then Playoff results. No rules for UI layout. Results must be saved in database, that could be erased to create a new tournament.  
  
---  
  

# `Solution: `
**I solved this task for 4 hours. Without using framework, to show you good skills in php.**  
  

## To start this project you need to:
1. `composer install`
2. `create database` 
3. `start redis`  
4. `start beanstalk`  
5. `edit .env as in .env_example`
6. `run migration php commands/migrate.php`
7. `start queue php commands/queue.php` 
8. `start php -S localhost:8080 inside public folder`  

What i can think could be better improve in this project:
1. **better configuration**
2. **better routing**
3. **dependency injection for service drivers**
4. **more detailed services**
5. **tests** 
  
Thank You, for this interesting task!