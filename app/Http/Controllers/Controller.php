<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Claneo SMT Backend Api Documentation",
     *      description="Claneo SMT BackendApi",
     *      @OA\Contact(
     *          email="mrudchenko@brightgrove.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Backend API Server"
     * )
     *
     * @OA\Tag(
     *     name="Auth",
     *     description="API Endpoints of Authentication"
     * )
     * @OA\Tag(
     *     name="Account",
     *     description="API Endpoints of Current User"
     * )
     * @OA\Tag(
     *     name="Roles",
     *     description="API Endpoints of Roles"
     * )
     * @OA\Tag(
     *     name="Users",
     *     description="API Endpoints of Users"
     * )
     * @OA\Tag(
     *     name="Clients",
     *     description="API Endpoints of Client Companies"
     * )
     * @OA\Tag(
     *     name="Projects",
     *     description="API Endpoints of Projects"
     * )
     * @OA\Tag(
     *     name="URLs",
     *     description="API Endpoints of URLs"
     * )
     * @OA\Tag(
     *     name="Keywords",
     *     description="API Endpoints of Keywords"
     * )
     * @OA\Tag(
     *     name="Events",
     *     description="API Endpoints of Events"
     * )
     *
     * @OA\SecurityScheme(
     *     type="http",
     *     scheme="header",
     *     name="bearerAuth",
     *     bearerFormat="JWT",
     *     securityScheme="bearerAuth"
     * )
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * parse csv file workflow
     *
     * @param $path - path of csv file
     * @param string $s - separator sign
     * @param false $stop - to prevent wrong parsing
     * @return array - array of parsed rows
     */
    protected function parseCsv($path, string $s = ';', bool $stop = false): array
    {
        $csv = [];
        $row = 1;

        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $s)) !== FALSE) {
                if ($stop && empty($csv) && count($data) < $stop) {
                    break;
                }
                $csv[] = $data;
                $row++;
            }

            fclose($handle);
        }

        return $csv;
    }
}
