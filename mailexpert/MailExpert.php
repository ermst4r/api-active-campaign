<?php ob_start();
require "vendor/autoload.php";

class MailexpertApi
{
    private $ac ;
    private  $to_api ;

    /**
     * @return mixed
     */
    public function getToApi()
    {
        return $this->to_api;
    }

    /**
     * @param mixed $to_api
     */
    public function setToApi($to_api)
    {
        $this->to_api = $to_api;
    }

    /**
     * @return mixed
     */
    public function getToApiKey()
    {
        return $this->to_api_key;
    }

    /**
     * @param mixed $to_api_key
     */
    public function setToApiKey($to_api_key)
    {
        $this->to_api_key = $to_api_key;
    }
    private  $to_api_key;


    public function __construct()
    {
        $this->ac = new ActiveCampaign('https://spareribexpress.api-us1.com', '98a01d79721b4448183b99f566422bfddbfbddae6fe287ecff7d3504c4a45d19c0108f30');

    }

    public function createContact($data)
    {
        $create_user = new ActiveCampaign($this->getToApi(),$this->getToApiKey());
        $response = $create_user->api('contact/add',$data);
        if ((int)$response->success) {
            return true;
        } else {
            return  $response->error;
        }
    }

    public function getFiliaal($id)
    {
        $response = $this->ac->api("contact/list?ids=".$id);
        if ((int)$response->success) {
            $res = get_object_vars($response);
            $fields = get_object_vars($res[0]->fields);
            return (isset($fields['11']->val) ? $fields['11']->val : '')  ;
        } else {
            return "";
        }
    }


    /**
     * @param int $limit
     * @return array
     */
    public  function syncUsers($limit=1,$filiaal_name)
    {

        for($i = 1; $i <= $limit; $i++) {
            $response = $this->ac->api("contact/list?page={$i}&full=0&filters[fields][%FILIAALNAAM%]=".$filiaal_name."&full=1");
            if ((int)$response->success) {
                // successful request
                foreach ($response as $key => $value) {
                    if(isset($value->id)) {
                        $user_data = get_object_vars($value);
                        $fields = get_object_vars($user_data['fields']);
                        if($fields[4]->val == 'ja') {
                            $insert_data = [
                                'first_name'=>$user_data['first_name'],
                                'last_name'=>$user_data['last_name'],
                                'email'=>$user_data['email'],
                                'field[%STRAATNAAM%]'=>$fields[2]->val,
                                'field[%MOBIELNUMMER%]'=>$fields[1]->val,
                                'field[%HUISNUMMER%]'=>$fields[3]->val,
                                'field[%WOONPLAATS%]'=>$fields[10]->val,
                                'field[%FILIAALNAAM%]'=>$fields[11]->val,
                            ];
                            $this->createContact($insert_data);

                        }
                    }
                }
            }
            else {
                // request error
                break;
            }
        }
    }
}





