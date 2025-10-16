<?php
namespace CanvasApiLibrary\Services;

use CanvasApiLibrary\Models\Domain;

class CanvasCommunicator{
    public function __construct(
        public readonly string $apiKey
    ){}

    private static function checkForStatusErrors(mixed $data): CanvasReturnStatus{
        if(!is_array($data)){
            return CanvasReturnStatus::SUCCESS;
        }
        if(isset($data["status"])){
            if($data["status"] == "not found"){
                return CanvasReturnStatus::NOT_FOUND;
            }
        }
        if(isset($data["errors"])){
            return CanvasReturnStatus::ERROR;
        }
        return CanvasReturnStatus::SUCCESS;
    }

    public function Get(string $route, Domain $domain) : array{
        return self::curlGet($domain->domain . $route, $this->apiKey);
    }

    public function Put(string $route, Domain $domain, mixed $data) : array{
        return self::curlPut($domain->domain . $route, $this->apiKey, $data);
    }

    //Static cURL calls
    /**
     * Summary of curlGet
     * @param mixed $url
     * @param mixed $apiKey
     * @throws \Exception
     * @return [mixed, CanvasReturnStatus]
     */
    protected static function curlGet($url, $apiKey): array {
        // echo "Fetching URL: $url<br>";
        // Initialize cURL
        $ch = curl_init($url);

        //Handling header reader to handle paginated results
        $nextURLHandler = new PaginationHeaderHandler();
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [&$nextURLHandler, "handle"]);

        // Set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ]);

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute
        $response = curl_exec($ch);

        // Handle errors
        if (curl_errno($ch)) {
            echo "cURL Error: " . curl_error($ch);
            throw new \Exception("cURL Error: " . curl_error($ch));
        } else {
            $data = json_decode($response, true);
        }

        // Close
        curl_close($ch);
        // var_dump($data);
        //if a next link for paginated results was found, call it recursively, append all results together.
        if($nextURLHandler->nextURL !== null){
            $topKey = null;
            if(!array_is_list($data)){
                //Non-list results need special handling to merge properly
                //Assume the top key is the one that contains the list of results
                $topKey = array_key_first($data);
                if(count($data) != 1 || !array_is_list($data[$topKey])){
                    throw new \Exception("Unexpected data structure when handling pagination for URL $url");
                }
                $data = $data[$topKey];
                $additionalData = self::curlGet($nextURLHandler->nextURL, $apiKey)[$topKey];
                $data = array_merge($data, $additionalData);
                $data = [$topKey => $data];
            }
            else{
                $additionalData = self::curlGet($nextURLHandler->nextURL, $apiKey);
                $data = array_merge($data, $additionalData);
            }
        }
        // echo "Total data: " . count($data) . "<br>";
        return [$data, self::checkForStatusErrors($data)];
    }

    /**
     * Summary of curlPut
     * @param mixed $url
     * @param mixed $apiKey
     * @param mixed $data
     * @throws \Exception
     * @return [mixed, CanvasReturnStatus]
     */
    protected static function curlPut($url, $apiKey, $data): array {
        // echo $url . "<br>";
        // echo "Fetching URL: $url<br>";
        // Initialize cURL
        $ch = curl_init($url);

        // Set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey"
        ]);

        // Set the HTTP method to PUT
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        $encoded = http_build_query($data);
        // Prepare JSON data
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Execute
        $response = curl_exec($ch);

        // Handle errors
        if (curl_errno($ch)) {
            echo "cURL Error: " . curl_error($ch);
            throw new \Exception("cURL Error: " . curl_error($ch));
        } else {
            $data = json_decode($response, true);
        }

        // Close
        curl_close($ch);
        return [$data, self::checkForStatusErrors($data)];
    }
}