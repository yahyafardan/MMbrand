<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the user is an admin
if ($_SESSION['role_name'] !== 'admin') {
    echo "Access denied.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Posts Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
            margin: 5%;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        h1 {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #4a90e2;
            margin: 0;
        }

        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #4a4a4a;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        input[type="submit"] {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #357abd;
        }

        .checkbox-group,
        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .checkbox-group label,
        .radio-group label {
            margin: 0;
            display: flex;
            align-items: center;
            font-weight: normal;
        }

        .social-media-inputs {
            margin-bottom: 16px;
        }

        .social-media-inputs div {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .social-media-inputs label {
            margin: 0 8px 0 0;
        }

        .social-media-inputs input[type="text"] {
            flex: 1;
            padding: 5px;
        }

        .link-input {
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <h1>Client Posts Form</h1>
    <div class="form-container">
        <form id="client-posts-form" action="submit_client_post.php" method="POST">
            <label for="client_name">Client Name:</label>
            <input type="text" id="client_name" name="client_name" required><br>

            <label for="n_of_posts">Number of Posts:</label>
            <input type="number" id="n_of_posts" name="n_of_posts" required><br>

            <label for="n_of_videos">Number of Videos:</label>
            <input type="number" id="n_of_videos" name="n_of_videos" required><br>

            <label>Days of Posting:</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="days_of_posting[]" value="monday"> Monday</label>
                <label><input type="checkbox" name="days_of_posting[]" value="tuesday"> Tuesday</label>
                <label><input type="checkbox" name="days_of_posting[]" value="wednesday"> Wednesday</label>
                <label><input type="checkbox" name="days_of_posting[]" value="thursday"> Thursday</label>
                <label><input type="checkbox" name="days_of_posting[]" value="friday"> Friday</label>
                <label><input type="checkbox" name="days_of_posting[]" value="saturday"> Saturday</label>
                <label><input type="checkbox" name="days_of_posting[]" value="sunday"> Sunday</label>
            </div>

            <label for="hashtags">Hashtags:</label>
            <textarea id="hashtags" name="hashtags" required></textarea><br>

            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required><br>

            <label for="duration">Duration (in months):</label>
            <input type="number" id="duration" name="duration" required><br>

            <label>Social Media Platforms:</label>
            <div class="checkbox-group" id="social-media-group">
                <label><input type="checkbox" name="social_media[]" value="X"> X</label>
                <label><input type="checkbox" name="social_media[]" value="instagram"> Instagram</label>
                <label><input type="checkbox" name="social_media[]" value="linkedin"> LinkedIn</label>
                <label><input type="checkbox" name="social_media[]" value="facebook"> Facebook</label>
                <label><input type="checkbox" name="social_media[]" value="youtube"> YouTube</label>
                <label><input type="checkbox" name="social_media[]" value="snapchat"> Snapchat</label>
                <label><input type="checkbox" name="social_media[]" value="tiktok"> TikTok</label>
            </div>

            <div class="social-media-inputs" id="social-media-inputs"></div>

            <label>Language:</label>
            <div class="radio-group">
                <label><input type="radio" name="language" value="arabic" required> Arabic</label>
                <label><input type="radio" name="language" value="english" required> English</label>
                <label><input type="radio" name="language" value="both" required> Both</label>
            </div>

            <input type="submit" value="Submit">
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('start_date');
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;

            const socialMediaCheckboxes = document.querySelectorAll('input[name="social_media[]"]');
            const socialMediaInputsContainer = document.getElementById('social-media-inputs');

            socialMediaCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', function () {
                    const platform = checkbox.value;
                    let inputDiv = document.querySelector(`.social-media-inputs div[data-platform="${platform}"]`);

                    if (checkbox.checked) {
                        if (!inputDiv) {
                            inputDiv = document.createElement('div');
                            inputDiv.setAttribute('data-platform', platform);
                            inputDiv.innerHTML = `
                                <label>${platform.charAt(0).toUpperCase() + platform.slice(1)} Link:</label>
                                <input type="text" name="${platform}_account_link" placeholder="Enter account link" class="link-input" required>
                            `;
                            socialMediaInputsContainer.appendChild(inputDiv);
                        }
                    } else {
                        if (inputDiv) {
                            socialMediaInputsContainer.removeChild(inputDiv);
                        }
                    }
                });
            });

            document.getElementById('client-posts-form').addEventListener('submit', function (event) {
                const daysCheckboxes = document.querySelectorAll('input[name="days_of_posting[]"]');
                const socialMediaCheckboxes = document.querySelectorAll('input[name="social_media[]"]');
                
                let oneDayChecked = false;
                let oneSocialMediaChecked = false;

                daysCheckboxes.forEach((checkbox) => {
                    if (checkbox.checked) {
                        oneDayChecked = true;
                    }
                });

                socialMediaCheckboxes.forEach((checkbox) => {
                    if (checkbox.checked) {
                        oneSocialMediaChecked = true;
                    }
                });

                if (!oneDayChecked) {
                    alert('Please select at least one day of posting.');
                    event.preventDefault();
                }
                if (!oneSocialMediaChecked) {
                    alert('Please select at least one social media platform.');
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
