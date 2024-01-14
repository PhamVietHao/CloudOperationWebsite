<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Count</title>
</head>
<body>
    <h1>Student Count</h1>

    <?php
        // Initialize AWS SDK for PHP (ensure you have AWS SDK installed)
        require 'vendor/autoload.php';  // Include the autoload file from AWS SDK

        use Aws\Ssm\SsmClient;

        // Replace 'your-region' with the actual AWS region
        $ssmClient = new SsmClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
        ]);

        // Replace with the actual names of the parameters in Parameter Store
        $parameterNames = [
            '/myapp/database/host',
            '/myapp/database/name',
            '/myapp/database/username',
            '/myapp/database/password',
        ];

        // Fetch parameter values from Parameter Store
        $parameterValues = [];
        foreach ($parameterNames as $parameterName) {
            $result = $ssmClient->getParameter([
                'Name' => $parameterName,
                'WithDecryption' => true,  // Decrypt the secure string parameter
            ]);
            $parameterValues[$parameterName] = $result['Parameter']['Value'];
        }

        // Database connection details
        $db_host = $parameterValues['/myapp/database/host'];
        $db_name = $parameterValues['/myapp/database/name'];
        $db_user = $parameterValues['/myapp/database/username'];
        $db_password = $parameterValues['/myapp/database/password'];

        // Create a database connection
        $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to get student count for each class
        $sql_cloud_operation = "SELECT COUNT(*) as count FROM students WHERE class='Cloud Operation'";
        $result_cloud_operation = $conn->query($sql_cloud_operation);
        $count_cloud_operation = $result_cloud_operation->fetch_assoc()["count"];

        $sql_intro_to_it = "SELECT COUNT(*) as count FROM students WHERE class='Introduction To IT'";
        $result_intro_to_it = $conn->query($sql_intro_to_it);
        $count_intro_to_it = $result_intro_to_it->fetch_assoc()["count"];

        // Display student count
        echo "<p>Number of students in Cloud Operation: $count_cloud_operation</p>";
        echo "<p>Number of students in Introduction To IT: $count_intro_to_it</p>";

        // Close the database connection
        $conn->close();
    ?>
</body>
</html>
