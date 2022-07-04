Movie List by Diego Alonso Alvarez Arcila

The web application consists in a SignUp and Login page, with input validations with regular expressions, 
and a page that looking for movies through an API.

First, create an account in the SignUp page, if everything is fine, when the button "Sign Up" is clicked,
a message is displayed right above the button, indicating that the account was created successfully. Then,
click in the Log In button for go to the Login page, enter username and the password, and if everything is fine,
you will be redirected to the movie search page.

In the SignUp and Login page, if the validations are not correct, an error message is displayed below the
correspondent input indicating what's wrong.

In the MovieList page, search the movie by the title or the year of release, the input title is required, the year
is optional, if the Search button is clicked, the results are displayed in a table, if the Save button is clicked,
a JSON file will be created with the results and the table will be displayed too. The table only displays a max of
100 entries and show 10 by page per default (the number of movies showed by page can be set by the user with a input
below the table), the table can be order by columns with a click in the header of the column that want to be order.

The button Log Out will close the session and you will be redirected to the login page.
