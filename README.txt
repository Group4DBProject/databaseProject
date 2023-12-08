To test transfering between accounts:
1. Go to index.html which acts as a login screen (Note we used Xampp to host the db and site)
2. Log in with the user info of username:test password:testpass (note the amount of money in the account)
3. Hit the back arrow then log in with username:login password:password 
4. Fill out the form with 25 in the amount and 1 in recipient account (This is the test account from step 2)
5. Log back into the account from step 2 and check the money amount, the balance will change and add another transaction.

To test depositing and withdrawing:
1. Log in with the user info username:admin password:adminpassword
2. Under deposit type in a 1 for the account id and 100 for the amount and select deposit in the dropdown, then hit submit
3. Log in with the user info username:test password:testpass and verify the funds have been deposited
4. Log in with the user info username:admin password:adminpassword
5. Under deposit type in a 1 for the account id and 100 for the amount and select withdraw in the dropdown, then hit submit
6. Log in with the user info username:test password:testpass and verify the funds have been withdrawn

If there is some error it is possible to use our test.php which should show most of the functionality. However the visual
frontend method is prefered.