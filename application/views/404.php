<!DOCTYPE html>
<html>
<head>
    <title>404 Page Not Found</title>
    <style>
        /* Add your custom CSS styling here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        
        .error-container {
            max-width: 500px;
            padding: 40px;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        
        p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 18px;
            transition: background-color 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .button:hover {
            background-color: #0056b3;
        }
        
        @media only screen and (max-width: 480px) {
            .error-container {
                padding: 20px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            p {
                font-size: 16px;
            }
            
            .button {
                padding: 10px 20px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <h1>404 Page Not Found</h1>
            <p>The page you requested could not be found.</p>
            <a href="/" class="button">Go to Homepage</a>
        </div>
    </div>
</body>
</html>
