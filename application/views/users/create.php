<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Add user</h1>
        <p class="text-muted mb-0">Register a new Admin or Cashier account.</p>
    </div>

    <div id="userAlert" class="alert d-none"></div>

    <div class="card shadow-sm" style="max-width: 520px;">
        <div class="card-body">
            <form id="userCreateForm">
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" maxlength="100" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" maxlength="150" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="role_id">Role</label>
                    <select id="role_id" name="role_id" class="form-select" required>
                        <option value="">Select role</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark">Register user</button>
                <a href="<?= site_url('users') ?>" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
