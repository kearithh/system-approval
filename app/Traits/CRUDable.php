<?php

namespace App\Traits;

trait CRUDable
{
    /**
     * Update record
     *
     * @param            $id
     * @param array|null $data
     * @return mixed
     */
    public function updateRecord($id, array $data = [])
    {
        $user       = auth()->user();

        $info    = $this->find($id);
        $changed = null;

        @$info->updated_at = date('Y-m-d H:i:s');
        @$info->updated_by = @$user->id;

        if (is_array($data) && $data) {
            foreach ($data as $key => $value) {
                $info->$key = $value;
            }
            $info->save();
        }
        return $info;
    }

    /**
     * Create record
     *
     * @param array|null $data
     * @return CRUDable
     */
    public function createRecord(array $data)
    {
        if (is_array($data) && $data) {

            $user       = auth()->user();

            @$this->created_by = @$user->id;
            @$this->updated_by = @$user->id;
            @$this->created_at = date('Y-m-d H:i:s');
            @$this->updated_at = date('Y-m-d H:i:s');

            foreach ($data as $key => $value) {
                $this->$key = $value;
            }

            $this->save();
        }
        return $this;
    }

    /**
     * Create record
     *
     * @param array|null $data
     * @return CRUDable
     */
    public function updateRecordTemplate(array $data)
    {
        if (is_array($data) && $data) {

            $user = auth()->user();

            // @$this->created_by = @$user->id;
            @$this->updated_by = @$user->id;
            @$this->updated_at = date('Y-m-d H:i:s');

            foreach ($data as $key => $value) {
                $this->$key = $value;
            }

            $this->save();
        }
        return $this;
    }
}
