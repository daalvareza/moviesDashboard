<?php

/**
 * Manages processes related to the search of movies
 */

class Movies
{
    /**
     * class properties
     * @var url url to pass it to the API
     */
    private $url;

    /**
     * Movies constructor
     */
    public function __construct()
    {
        $this->url = "https://www.omdbapi.com/?apiKey=fc59da33";
    }

    /**
     * Function define the curl to the API
     * @param string $url string with the url to send it to the API
     * @return array data returned by the API
     */
    public function curlAPI($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $info = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($info, true);
        return $data;
    }

    /**
     * Make the curl and paginate the url with their page param and return all the data
     * @param string $url string with the url with the filters to send it to the API
     * @return array data with all the movies returned by the API
     */
    public function requestInfo($url)
    {
        // Do the curl
        $data = $this->curlAPI($url);
        // Get the total results of the API
        $totalResults = $data["totalResults"];
        // Get the movies returned in the first page        
        $data = $data["Search"];
        // The API always returned 10 movies by page, if there is more results, paginate
        if ($totalResults > 10) {
            // Initialize the pagination from 2
            $page = 2;
            // Get the total pages
            $total = ceil($totalResults / 10);
            // Paginate till the page 10 (100 movies max), due to perfomance linked with the API
            while ($page <= $total && $page <= 10) {
                // Cut the param page of the url if there is on it
                if (strpos($url, "&page")) {
                    $url = explode("&page", $url)[0];
                }
                // Concatenate the page param and the number of the page
                $url .= "&page=" . $page;
                // Do the request to the API
                $dataPage = $this->curlAPI($url);
                // Push the movies of the current page in the data
                array_push($data, ...$dataPage["Search"]);
                $page++;
            }
        }
        return $data;
    }

    /**
     * Function to build the URL with the filters provided
     * @param string $title title of the movie, required
     * @param string $year year of release of the movie, optional
     * @return string url with the params setted
     */
    public function buildURL($title, $year = "")
    {
        // Get the default url
        $url = $this->url;
        // Concatenate the title param
        $url.= "&s=" . $title;
        // If the year param is passed, concatenate it too
        if (!empty($year)) {
            $url.= "&y=" . $year;
        }
        return $url;
    }
    /**
     * Method that build tables with pagination and orderable columns.
     * Ex: dataArray => [
    ['title' => 'Title 1','country' => 'Usa','region' => 'Region 1','city' => 'Miami'  ,'campaign' => 'c1',],
    ['title' => 'Title 2','country' => 'Usa','region' => 'Region 2','city' => 'Chicago','campaign' => 'c2',]];
     * Ex: $columsSettings => [
    "Title"    => ["orderable" => "title"],
    "Campaign" => ["orderable" => "campaign"],
    ];
     * Ex: $paginationSettings => [
    "pagination" => "yes",
    "rowsToShow" => 25,
    "inputShow" => "no",
    "hitsNumber" => "yes",
    ];
     * pagination => yes : show and enable the pagination of the table
     * rowsToShow : number of rows to show when rendered the table, default is 25 when pagination is yes,
     *              or the length of the dataArray when not
     * inputShow => yes : show an input to change the rows to show in any moment
     * hitsNumber => yes : show a string that informs the user the current page, total pages and number of entries
     * The value by default in all the keys of paginationSettings, except rowsToShow, is yes
     * The method works with the following parameters:
     * @param array $dataArray Data to be displayed in the table
     * @param string $id Custom ID for the table, used for JavaScript purposes
     * @param array $columsSettings Modifies the header of each column in table and makes the column orderable or not
     * @param array $rowClass array build to add a class to the rows
     * @param array $rowId array build to add id to the rows
     * @param array $paginationSettings array to build the pagination
     * @param string $facets HTML extra to put in the header of the table
     * @return string HTML table
     */
    public function jsDataTable
    (
        $dataArray,
        $id = "",
        $columsSettings = array(),
        $rowClass = array(),
        $rowId = array(),
        $paginationSettings = array(),
        $facets = ""
    )
    {
        if(!$dataArray) return false;

        // If dataArray is not multidimensional, create columns for keys and values
        $firstEntryValue = $dataArray[0];
        if(!is_array($firstEntryValue)){
            foreach((array) $dataArray as $k=>$v){
                $line["key"] =  "<b>$k</b>";
                $line["value"] = $v;
                $lines[] = $line;
            }
            $dataArray = $lines;
        }

        // Getting the list of all possible keys
        foreach ((array)$dataArray as $k => $v) {
            foreach ((array)$v as $sk => $sv) {
                $keymap[$sk] = $sk;
            }
        }

        // If id for the table is not passed, create a unique id
        if ($id == "") {
            $id = "table-" . uniqid();
        }

        // Loop through rowClass array to create array class
        if(isset($rowClass)) {
            foreach ((array)$rowClass as $k => $v) {
                if ($v != "") {
                    $classes[] = "class=\"" . strtolower($v) . "\"";
                } else {
                    $classes[] = "";
                }
            }
        }

        // Loop through rowId array to create array id
        if(isset($rowId)) {
            foreach ((array)$rowId as $k => $v) {
                if ($v != "") {
                    $ids[] = "id=\"" . strtolower($v) . "\"";
                } else {
                    $ids[] = "";
                }
            }
        }

        // Declaring variables to build the table
        $body        = "";
        $headerBuilt = "";
        $header      = "";

        // Set the number of rows to show
        if ($paginationSettings["rowsToShow"]) {
            $rowsToShow = $paginationSettings["rowsToShow"];
        } else if ($paginationSettings["pagination"] == "no") {
            $rowsToShow = count($dataArray);
        } else {
            $rowsToShow = 25;
        }

        // Build the values
        $i = 0;
        $hideStyle = "";
        foreach ((array)$dataArray as $k => $v) {
            // To build the body
            $body .= "<tr $hideStyle $ids[$i] $classes[$i]>";
            foreach ((array)$keymap as $sk => $sv) {
                // To build the header
                if ($headerBuilt != "1") {
                    $settings = array();
                    if (isset($columsSettings[$sk])) $settings = $columsSettings[$sk];
                    // Open tag
                    $header .= "\n\t<th";
                    // Set column title
                    if (isset($settings["title"])) $header .= " title='{$settings["title"]}' ";
                    // Set if column is orderable and key to send when ordering by this column (aria-label)
                    if (isset($settings["orderable"]) && !$settings["orderable"]) $header .= " data-orderable='false' ";
                    else if (isset($settings["orderable"])) $header .= " aria-label='{$settings["orderable"]}' onclick='js_orderColumn(this, \"{$id}\")' class='order_def' aria-sort='desc'";
                    else if (isset($columsSettings["orderable_all"]) && $columsSettings["orderable_all"] == "true") $header .= "";
                    else $header .= " data-orderable='false' ";
                    $header .= ">";
                    // Set column label
                    if (isset($settings["label"])) $header .= $settings["label"];
                    else  $header .= $sk;
                    // Close tag
                    $header .= "</th>";
                }

                $content = $v[$sv];
                if(is_array($v[$sv])) $content = json_encode($v[$sv]);

                $body .= "\n <td>{$content}</td>";
            }
            $headerBuilt = 1;
            $body .= "</tr>";
            $i++;

            /* All the rows are rendered, but if the param rowsToShow is passed in paginationSettings
             * just show the desired number of rows and hide the others */
            if ($i == $rowsToShow) {
                $hideStyle = 'style = "display: none;"';
            }
        }

        // To build the footer
        $footer = "";
        if (isset($columsSettings["footer"]) && $columsSettings["footer"] == "true") {
            $footer = "<tfoot><tr>{$header}</tr></tfoot>";
        }

        // HTML of the table
        $dataTable = <<<HTML
        <table id='$id' class='moviesTable'>
          <thead><tr>{$header}</tr></thead>
          <tbody>{$body}</tbody>
          {$footer}
        </table>
HTML;

        // Calculate the number of pages based in the number of entries and rows showed
        $totalHits = $i;
        $totalPages = ceil($totalHits / $rowsToShow);

        // Pagination Settings validations
        $paginationSettings["pagination"] = $paginationSettings["pagination"] ?? "yes";
        $paginationSettings["inputShow"]  = $paginationSettings["inputShow"] ?? "yes";
        $paginationSettings["hitsNumber"] = $paginationSettings["hitsNumber"] ?? "yes";
        $pagination = "";
        $inputShow = "";
        $hitsNumber = "";

        // Validate if the numeric pagination is desired
        if ($paginationSettings["pagination"] == "yes") {
            // Create the HTML for the numeric pagination
            $pagination = $this->jsNumericPagination($id, $totalPages, $rowsToShow);
        }

        // Validate if the input for change the showed rows is desired
        if ($paginationSettings["inputShow"] == "yes") {
            // Create the HTML for the input
            $inputShow = <<<HTML
            <div id="PageShow-$id" class="tablePageShow">
                <span>Show <input type='text' placeholder='length' name='length'  value='{$rowsToShow}' style='width:60px'></span>
            </div>
HTML;
        }

        // Validate if the number of hits per page is desired
        if ($paginationSettings["hitsNumber"] == "yes") {
            // Create the HTML for the hits
            $hitsNumber = <<<HTML
            <div id="PageHits-$id" class="tablePageHits">
                <div class='moviesTableResultsFiltered'>Page 1 of {$totalPages} ({$totalHits} entries) </div>
            </div>
HTML;
        }

        $html = <<<HTML
            <div>
                <div class="tableFacets">
                    $facets
                </div> 
                <div class="tableInfo">
                    $hitsNumber
                </div>
                <div class="tableHTML moviesAdvancedTable">$dataTable</div>
                <div class="tablePagination">
                    $inputShow
                    <div class="tablePageSelector">$pagination</div>
                </div>
                <script>
                    // Listener for the input for change the number of showed rows
                    $('#PageShow-$id span input').on('keypress', function (e) {
                        // Enter key while the input is focus
                        if (e.which === 13) {
                            const rowsToShow = $(this).val();
                            const totalPages = Math.ceil($totalHits/rowsToShow);
                            updateNumericPagination('$id', 1, totalPages, rowsToShow);
                        }
                    });
                </script>
            </div>
HTML;

        return $html;
    }

    /**
     * Method that build a pagination system that is used in data table with pagination method
     * @param string $tableId id of the table that requires pagination
     * @param int $totalPages number of the total pages
     * @param int $length number of entries in each page
     * returns string simple numeric pagination system
     */
    private function jsNumericPagination($tableId, $totalPages = 1, $length = 25)
    {
        $startPage = 1;
        $pageLimit = 6;
        $length = htmlspecialchars($length);

        $out = "<div id='pagination-{$tableId}' class='movies-pagination'>";

        // Go to the first page and previous page buttons
        $out .= "<span data-page='1' class='pagination-unavailable'>
                    <img class='center-icon' src='img/skip_to_start.png'>
                </span>";
        $out .= "<span class='pagination-unavailable'>
                    <img class='center-icon' src='img/sort_left.png'>
                </span>";

        $out .= "<div class='pagination-nums'>";

        // Show max 6 numbers in the numeric pagination
        if ($totalPages > $pageLimit) {
            $start = ($startPage < $pageLimit) ? 1 : $startPage - $pageLimit + 2;
            $end = ($startPage + 1 <= $pageLimit) ? $pageLimit : (($startPage + 1 >= $totalPages) ? $totalPages : ($startPage + 1));
        } else {
            $start = 1;
            $end = $totalPages;
        }

        // Numeric buttons
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $startPage) $out .= "<div id='page-{$i}' class='numeric-pagination-button page-selected page-unavailable'>{$i}</div>";
            else $out .= "<div class='numeric-pagination-button page-available' id='page-{$i}' onclick=\"js_gotoPage(this,'$tableId',$totalPages,$length)\">{$i}</div>";
        }

        // If there are more pages after the six showed, show a "..." button
        if ($totalPages > $end) {
            $out .= "<div id='page-{$i}'> ... </div>";
        }

        $out .= "</div>";

        // Go to the last page and next page buttons
        if ($totalPages > 1) {
            $out .= "<span class='page-available pagination-button' onclick=\"js_nextPage('$tableId',$totalPages,$length)\">
                    <img class='center-icon' src='img/sort_right.png'>
                </span>";
            $out .= "<span data-page='{$totalPages}' class='page-available pagination-button' onclick=\"js_gotoPage(this,'$tableId',$totalPages,$length)\">
                    <img class='center-icon' src='img/end_icon.png'>
                </span>";
        } else {
            $out .= "<span class='pagination-unavailable'>
                    <img class='center-icon' src='img/sort_right.png'>
                </span>";
            $out .= "<span data-page='{$totalPages}' class='pagination-unavailable'>
                    <img class='center-icon' src='img/icon=end_icon.png'>
                </span>";
        }

        $out .= "</div>";

        return $out;
    }
}