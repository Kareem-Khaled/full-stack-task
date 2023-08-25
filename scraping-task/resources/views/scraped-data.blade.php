<!DOCTYPE html>
<html>
<head>
    <title>Scraped Data</title>
</head>
<body>
    <h1>Scraped Book Data</h1>
    <button id="scrapeBtn">Scrape Data</button> <!-- Add a button to trigger the scraping -->
    <ul id="scrapedData">
        <!-- Display the scraped data here -->
    </ul>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Attach click event to the "Scrape Data" button
        $(document).on('click', '#scrapeBtn', function () {
            $.ajax({
                url: '{{ route("scrape") }}', // Replace with your actual route name
                type: 'GET',
                success: function (response) {
                    console.log(response);
                    $('#scrapedData').html(response.html); // Update the view with the new data
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
    </script>
</body>
</html>
