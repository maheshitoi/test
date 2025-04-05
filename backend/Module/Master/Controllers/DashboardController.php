<?php
namespace App\Controllers;
use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use App\Infrastructure\Persistence\Member\SQLMemberRepository;
use Core\Models\Utility\UtilityModel;
use Core\Controllers\BaseController;

class DashboardController extends BaseController
{

    private $repository;
    private $role_repository;
    private $utility_repo;
    private $member_req;
    private $member;

    public function __construct()
    {
        $this->initializeFunction();
        $this->utility_repo         = new UtilityModel();
        $this->member_req = new SQLMember_requestRepository();
        $this->member  =new SQLMemberRepository();
    }

    public function index()
    {

    }

    public function getData()
    {
        $data['total_members'] = count($this->member->findAll());
        $data['total_member_request'] = count($this->member_req->findAll());
        return $this->message(200, $data);

    }
    // member,m_req,active eve and inactive

    public function getWhetherUrl($city)
    {
        $client   = \Config\Services::curlrequest();
        $url      = 'https://forecast7.com/api/';
        $data = $this->getDataFromUrl('json');
        $response = $client->request('GET', $url . 'autocomplete/' . str_replace(" ", '-', $city));
        $res      = $response->getBody();
        $result      = json_decode($res)[0] ?? '';
        if ($result) {
            $response = $client->request('GET', $url . 'getUrl/' . str_replace(" ", '-', $result->place_id));
            $urls  =  $response->getBody();
            $result->data_url = $urls;
        }
        foreach ($data as $key => $value) {
            $result->{$key} =$value;
        }
        return $this->message(200, $result);
    }
    public function getallcount()
    {
        $doctorcount = count($this->member->findAllByWhere(['field'=>'dentist']));
        $studentcount = count($this->member->findAllByWhere(['field'=>'student']));
        $data ['field_count'] = [$doctorcount, $studentcount];

        $data ['member_count'] = $this->member->findAllDataByMonth();
        return $this->message(200, $data);

    }

}
