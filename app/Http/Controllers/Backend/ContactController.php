<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Routing\Controller;
use App\Models\Contact;
use App\Traits\HasCrudActions;

class ContactController extends Controller
{
    use HasCrudActions;
    public $model = Contact::class;

    private function getTable()
    {
        return 'FormContacts';
    }

    private function beforeIndex($query)
    {
        return $query->where('type', 'CONTACT_FORM')
            ->orderBy('id', 'DESC');
    }

    public function updateModel($id, $data)
    {
        $resource = $this->model::query();

        if (!is_null($resource->getMacro('withTrashed'))) {
            $resource = $resource->withTrashed();
        }

        $resource = $resource->findOrFail($id);

        // Bảo vệ data: Nếu payload gửi data là null hoặc rỗng, loại bỏ để tránh ghi đè thông tin khách hàng
        if (array_key_exists('data', $data) && empty($data['data'])) {
            unset($data['data']);
        }

        $resource->update($data);

        return $resource;
    }
}
