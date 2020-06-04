# Automated Testing for Yclas

## Instructions


1. Install Yclas. 

    Admin must have:<br>
    Email: admin@gmail.com<br>
    Password: 1234


2. Upload all premium themes into /themes folder.


3. Download [this file](https://cdn.rawgit.com/yclas/yclas/master/oc/tests/_data/import_ads_test.csv) and upload it on panel, Tools -> Import. Then, click PROCESS. 


4. Go to the /oc/ directory to download codecept.phar and run the first test:

		cd oc/
		wget http://codeception.com/codecept.phar
        php codecept.phar run acceptance SetUsersPasswordsCept


    This test changes the passwords of users.


5. Run all the tests:

        php codecept.phar run acceptance default/

    To see all the steps for each test run this command (Optional, not recommended for this case)

        php codecept.phar run acceptance default/ --steps



    
## Generate scenarios

Generates user-friendly text scenarios from scenario-driven tests.

    php codecept.phar g.scenarios acceptance default/ --path tests/docs
    

    

