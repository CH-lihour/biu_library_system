<?php

namespace App\Http\Controllers;

use App\DataTables\PublisherDataTable;
use App\Http\Requests\Publisher\StorePublisherRequest;
use App\Http\Requests\Publisher\UpdatePublisherRequest;
use App\Services\PublisherService;
class PublisherController extends Controller
{

    public function __construct(protected PublisherService $publisherService)
    {

    }
    public function index(PublisherDataTable $dataTable)
    {
        return $dataTable->render('publishers.index');
    }

    public function create()
    {
        return view('publishers.create');
    }

    public function store(StorePublisherRequest $request)
    {
        $this->publisherService->createPublisher($request->validated());

        return to_route('publishers.index')->with('success', 'Publisher created successfully.');
    }

    public function edit(int $id)
    {
        $publisher = $this->publisherService->getPublisher($id);
        return view('publishers.edit', compact('publisher'));
    }

    public function update(UpdatePublisherRequest $request, int $id)
    {
        $this->publisherService->updatePublisher($id, $request->validated());

        return to_route('publishers.index')->with('success', 'Publisher updated successfully.');
    }

    public function destroy(int $id){
        $this->publisherService->deletePublisher($id);

        return to_route('publishers.index')->with('success', 'Publisher deleted successfully.');
    }
}
