README.txt



HOW TO RUN THE PROJECT

First make sure XAMPP is installed and both Apache and MySQL are turned on. Then open phpMyAdmin in your browser and create a new database called library_system. After that go to the Import tab and import the database_export.sql file I provided. This will create the books table and also add 5 sample books automatically so you can see the system working right away.

Once the database is ready just copy library_system.php into your htdocs folder. For example you can make a folder called library inside htdocs and put the file there. Then open your browser and go to http://localhost/library/library_system.php and the system will load.


WHAT I BUILT AND HOW

I built this as a single PHP file that handles everything together. The reason I kept it in one file is because it is simpler to follow and easier to run without any extra setup. All the PHP logic sits at the top and the HTML comes after it.

The first thing I did was set up the database connection using mysqli. I also added an error check right after the connection so if something goes wrong it shows a proper message instead of just crashing silently. Then I wrote a CREATE TABLE IF NOT EXISTS query so the table gets created automatically the first time the file runs. The table has columns for id, title, author, year, status and created_at.

For adding books I made a form that submits using POST. Before inserting anything into the database I check that no field is empty and that the year is between 1000 and 2025. If validation fails I show an error message right above the form. If it passes I use a prepared statement to insert the data which keeps the database safe from SQL injection. After inserting I show a success message.

For viewing books I wrote a SELECT query with ORDER BY id DESC so the newest books always appear at the top of the table. The table shows ID, Title, Author, Year, Status and an Actions column with buttons.

For editing I used a GET parameter called edit. When someone clicks the Edit button it sends the book ID in the URL and I fetch that book's data from the database and pre-fill all the form fields with it. When the user submits the edit form it runs a prepared UPDATE statement with a WHERE id clause so only that specific book gets updated and nothing else is touched.

For deleting I added a JavaScript onclick confirm dialog that says "Are you sure you want to delete this book?" before anything gets deleted. If the user clicks OK then the delete runs using a prepared statement with the book ID validated as an integer. After deleting I redirect the page so refreshing does not accidentally delete again.

For search I made a form at the top that sends the search term through GET. I use a LIKE query with percent signs on both sides so it matches anything that contains the word the user typed. It searches through both title and author columns. If nothing is typed it just shows all books.

I also implemented the status toggle bonus feature. Every book shows either Available in green or Borrowed in orange. There is a button next to each book that lets you switch the status with one click. It reads the current status first and then flips it to the other one and saves it.

For styling I kept the design clean and simple. I used different background colors for different buttons so Add is green, Edit is blue and Delete is red. The table rows highlight in a light blue when you hover over them. The whole page is centered in a white container with a light grey background so it looks neat.

For security I used prepared statements on every query that touches user input. I also used htmlspecialchars on all output so nothing stored in the database can break the HTML or run as a script.


FILES IN THIS SUBMISSION

library_system.php is the main file with all the code. database_export.sql has the table structure and five sample books. README.txt is this file explaining everything.


BONUS FEATURES IMPLEMENTED

Status toggle between Available and Borrowed with color indicators is done. All queries use prepared statements which also counts as the input sanitization bonus.
