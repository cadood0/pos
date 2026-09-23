<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Users</h1>
            <p class="text-muted mb-0">Create staff accounts and assign roles.</p>
        </div>
        <a href="<?= site_url('users/create') ?>" class="btn btn-dark">
            Add user
        </a>
    </div>

    <div id="userAlert" class="alert d-none"></div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="userRows"></tbody>
            </table>
        </div>
    </div>
</div>
