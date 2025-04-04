<?php
namespace Core\Controllers;

trait DMLController
{
    public function getList($tblLazy, $active=true)
    {
        $ftbl;
        $isActive = ($active === 'false') ? false : true;
        if ($tblLazy) {
            $ftbl = json_decode(utf8_decode(urldecode($tblLazy)));
        }
        $data = $this->repository->findAllPagination($ftbl, $isActive);
        return $this->message(200, $data, 'Success');
    }

    public function get($id = false)
    {
        $id = $id ? $id : 0;
        $data;
        if ($id === 0) {
            $data = $this->repository->findAll();
        } else {
            $data = $this->repository->findById($id);
        }
        return $this->message(200, $data);
    }

    public function save()
    {
        $req = $this->getDataFromUrl('json');
        if (!empty($req)) {
            $data = $this->repository->save($req);
            return $this->message(200, $data,'success');
        } else {
            return $this->message(400, null, 'Date Not Available');
        }
    }

    public function update($id)
    {
        $data = $this->getDataFromUrl('json');
        if (!empty($id)) {
            $data = $this->repository->updateById($id, $data);
            return $this->message(200, $data);
        } else {
            return $this->message(400, null, 'Date Not Available');
        }
    }

    public function delete($id)
    {
        $id = (int) $id;
        if ($id) {
            if ($this->repository->deleteOfId($id)) {
                return $this->message(200, $id, 'Success');
            } else {
                return $this->message(400, null, 'Failed to Delete');
            }

        }
    }

}
