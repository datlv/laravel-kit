<?php namespace Datlv\Kit\Controllers\Tools;

use Illuminate\Http\Request;
use Datlv\Kit\Extensions\BackendController;
use Datlv\Kit\Extensions\ImportRequest;

/**
 * Class ImportController
 * @package App\Http\Controllers\Backend
 * @author Minh Bang
 */
class ImportController extends BackendController
{
    /**
     * Show form upload Excel file
     * @param string $resource
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function step1($resource)
    {
        $importer = $this->getImporterOrFail($resource);
        $title = $importer->title();
        $this->buildHeading([trans('kit::import.import'), $title],
            'fa-cloud-upload ',
            [
                route("backend.{$resource}.index") => $title,
                '#' => trans('kit::import.import')
            ]
        );
        $url = route("backend.tools.import.step2", compact('resource'));
        return view("kit::backend.tool.import.step1", compact('url', 'importer'));
    }

    /**
     * Save file to tmp, read data => show info
     * @param \Datlv\Kit\Extensions\ImportRequest $request
     * @param string $resource
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function step2(ImportRequest $request, $resource)
    {
        $importer = $this->getImporterOrFail($resource);
        $title = $importer->title();
        $this->buildHeading([trans('kit::import.import'), $title],
            'fa-cloud-upload ',
            [
                route("backend.{$resource}.index") => $title,
                '#' => trans('kit::import.import')
            ]
        );
        $data = $importer->load($request->file('file'))->all();

        if ($data) {
            $url = route("backend.tools.import.step3", compact('resource'));
            return view("kit::backend.tool.import.step2", compact('resource', 'url', 'data', 'importer'));
        } else {
            $url = route("backend.tools.import.step2", compact('resource'));
            $message = trans('kit::import.empty_data');
            $importer->deleteTmpFile();
            return view("kit::backend.tool.import.step1", compact('url', 'message', 'importer'));
        }
    }

    /**
     * Phân tích lại file lần nữa (có remap key), lưu DB, báo thành công, xóa file tạm
     * @param \Illuminate\Http\Request $request
     * @param $resource
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function step3(Request $request, $resource)
    {
        $importer = $this->getImporterOrFail($resource);
        $title = $importer->title();
        $this->buildHeading([trans('kit::import.import'), $title],
            'fa-cloud-upload ',
            [
                route("backend.{$resource}.index") => $title,
                '#' => trans('kit::import.import')
            ]
        );
        $count = $importer->load($request->get('filename'))->import();
        $importer->deleteTmpFile();
        $message = $count === false ? trans('kit::import.import_fail') : trans('kit::import.import_success', compact('count'));
        $type = $count === false ? 'danger' : 'success';
        $step1_title = $count === false ? trans('kit::import.try_again') : trans('kit::import.another_file');
        return view("kit::backend.tool.import.step3", compact('resource', 'title', 'message', 'type', 'step1_title'));
    }

    /**
     * @param string $resource
     * @return \Datlv\Kit\Extensions\Importer
     */
    protected function getImporterOrFail($resource)
    {
        $importer = config("kit.importers.{$resource}");
        abort_unless($importer, 404, 'Importer for this resource is undefined!');
        return new $importer();
    }
}
