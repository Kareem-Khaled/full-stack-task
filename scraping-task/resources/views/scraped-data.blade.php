<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scraped Data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-3">Scraped Book Data</h1>
        <p id="bookCount" data-num="{{ count($data) }}">Total Books: {{ count($data) }}</p>
        <button id="scrapeButton" class="btn btn-success" onclick="scrapeData()">Scrape New Data</button>
        <div id="scrapedData" class="mt-3">
            @foreach ($data as $item)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $item->author }}</h6>
                        <p class="card-text"><strong>الفسم:</strong> {{ $item->section }}</p>
                        <p class="card-text"><strong>معلومات إضافية:</strong></p>
                        <ul>
                            @foreach (json_decode($item->info) as $infoItem)
                                <li>{{ $infoItem->label }}: {{ $infoItem->value }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function scrapeData() {
            $("#scrapeButton").prop("disabled", true);

            $.ajax({
                url: '{{ route("scrape") }}',
                type: 'GET',
                dataType: 'json',
                timeout: 3 * 60 * 1000,
                success: function (response) {
                    if (response && response.data) {
                        console.log(response);
                        let bookCount = $("#bookCount");
                        const scrapedData = $("#scrapedData");
                        const data = response.data;

                        for (const book of data) {
                            const card = $("<div>").addClass("card mb-3");
                            const cardBody = $("<div>").addClass("card-body");
                            const title = $("<h5>").addClass("card-title").text(book.title);
                            const author = $("<h6>").addClass("card-subtitle mb-2 text-muted").text(book.author);
                            const section = $("<p>").addClass("card-text").html("<strong>الفسم:</strong> " + book.section);
                            const infoList = $("<ul>");

                            for (const infoItem of book.info) {
                                const infoItemLi = $("<li>").text(infoItem.label + ": " + infoItem.value);
                                infoList.append(infoItemLi);
                            }

                            cardBody.append(title, author, section, "<strong>معلومات إضافية:</strong>", infoList);
                            card.append(cardBody);
                            scrapedData.append(card);
                        }

                        const initialCount = parseInt($("#bookCount").attr("data-num"));
                        const newCount = initialCount + data.length;
                        $("#bookCount").text("Total Books: " + newCount).attr("data-num", newCount);

                    } else {
                        console.error("Empty response or incorrect format.");
                    }
                },
                complete: function () {
                    $("#scrapeButton").prop("disabled", false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX request failed:");
                    console.error("Status:", textStatus);
                    console.error("Error:", errorThrown);
                }

            });
        }
    </script>
</body>
</html>
