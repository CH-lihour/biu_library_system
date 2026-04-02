<div>
    <a href="{{ route('books.edit', $query->id) }}" class="btn btn-sm btn-warning">
        <i class="fa fa-edit"></i>
    </a>
    <form action="{{ route('books.destroy', $query->id) }}" method="POST" class="d-inline-block js-delete-form">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-sm btn-danger js-delete-btn">
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>
