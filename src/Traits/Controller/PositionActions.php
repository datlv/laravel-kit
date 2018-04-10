<?php
namespace Datlv\Kit\Traits\Controller;

use Request;

/**
 * Class PositionActions
 *
 * @package Datlv\Kit\Traits\Controller
 */
trait PositionActions
{
    /**
     * Lấy model từ $id
     * Model phải sử dụng PositionTrait
     *
     * @param integer $id
     *
     * @return \Datlv\Kit\Extensions\Model
     */
    abstract protected function positionModel($id);

    /**
     * @return string
     */
    abstract protected function positionName();

    /**
     * @param string $name
     *
     * @return string
     */
    protected function positionModelId($name)
    {
        $id = Request::get($name, '');
        return str_replace('row-', '', $id);
    }

    /**
     * Ajax post cập nhật order (position) của model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order()
    {
        $ok = true;
        $id = $this->positionModelId('id');
        $id_prev = $this->positionModelId('id_prev');
        $id_next = $this->positionModelId('id_next');
        if ($id && ($manufacturer = $this->positionModel($id)) && ($id_prev || $id_next)) {
            if ($id_prev) {
                if ($manufacturer_prev = $this->positionModel($id_prev)) {
                    $manufacturer->moveAfter($manufacturer_prev);
                } else {
                    $ok = false;
                }
            } else {
                if ($manufacturer_next = $this->positionModel($id_next)) {
                    $manufacturer->moveBefore($manufacturer_next);
                } else {
                    $ok = false;
                }
            }
        } else {
            $ok = false;
        }
        return response()->json(
            [
                'type'    => $ok ? 'success' : 'error',
                'content' => trans(
                    $ok ? 'common.position_object_success' : 'common.object_not_found',
                    ['name' => $this->positionName()]
                ),
            ]
        );
    }
}
