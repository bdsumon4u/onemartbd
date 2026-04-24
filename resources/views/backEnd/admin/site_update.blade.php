@extends('backEnd.admin.layouts.master')

@section('title')
    Site Update
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <div class="row">
                    <div class="col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Site Update</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Site Update</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3">Deployment Controls</h4>
                                <p class="text-muted mb-4">
                                    This updater checks your git remote and runs deployment in background (git pull,
                                    composer install, migrate, cache refresh). Site may go down during update, then it
                                    is automatically brought back up.
                                </p>

                                <div class="d-flex flex-wrap" style="gap: 10px;">
                                    <button id="check-update-btn" type="button" class="btn btn-primary">
                                        Check Update
                                    </button>
                                    <button id="run-update-btn" type="button" class="btn btn-success" disabled>
                                        Run Update
                                    </button>
                                </div>

                                <div id="force-reset-warning" class="alert alert-danger mt-3 mb-0 d-none">
                                    <strong>History Diverged:</strong> Remote history changed or local has extra commits.
                                    Running update will discard local commits by hard reset.
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="force-reset-checkbox">
                                        <label class="form-check-label" for="force-reset-checkbox">
                                            I understand local commits will be discarded.
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div id="update-alert" class="alert alert-info mb-0" role="alert">
                                        Click "Check Update" to detect latest commits.
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Remote:</strong> <span id="remote-name">-</span></li>
                                        <li><strong>Branch:</strong> <span id="branch-name">-</span></li>
                                        <li><strong>Local Commit:</strong> <code id="local-commit">-</code></li>
                                        <li><strong>Remote Commit:</strong> <code id="remote-commit">-</code></li>
                                        <li><strong>Sync State:</strong> <span id="sync-state">-</span></li>
                                        <li><strong>Requires Reset:</strong> <span id="requires-reset">-</span></li>
                                        <li><strong>Last Update:</strong> <span id="last-update-at">-</span></li>
                                        <li><strong>Status:</strong> <span id="run-status">idle</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        (function() {
            const checkButton = document.getElementById('check-update-btn');
            const runButton = document.getElementById('run-update-btn');
            const alertBox = document.getElementById('update-alert');
            const remoteName = document.getElementById('remote-name');
            const branchName = document.getElementById('branch-name');
            const localCommit = document.getElementById('local-commit');
            const remoteCommit = document.getElementById('remote-commit');
            const syncState = document.getElementById('sync-state');
            const requiresReset = document.getElementById('requires-reset');
            const lastUpdateAt = document.getElementById('last-update-at');
            const runStatus = document.getElementById('run-status');
            const forceResetWarning = document.getElementById('force-reset-warning');
            const forceResetCheckbox = document.getElementById('force-reset-checkbox');
            let pollTimer = null;
            let isUpdateAvailable = false;
            let resetRequired = false;
            let expectStatusUpdates = false;
            let statusErrorCount = 0;

            function setAlert(type, message) {
                alertBox.className = 'alert alert-' + type + ' mb-0';
                alertBox.textContent = message;
            }

            function setButtons(isChecking, canRun) {
                checkButton.disabled = isChecking;
                runButton.disabled = !canRun;
            }

            function refreshRunButtonState() {
                const canRun = isUpdateAvailable && (!resetRequired || forceResetCheckbox.checked);
                setButtons(false, canRun);
            }

            function setCommitValues(data) {
                remoteName.textContent = data.remote ?? '-';
                branchName.textContent = data.branch ?? '-';
                localCommit.textContent = data.local_commit ?? '-';
                remoteCommit.textContent = data.remote_commit ?? '-';
                syncState.textContent = data.sync_state ?? '-';
                requiresReset.textContent = data.requires_force_reset ? 'yes' : 'no';
                lastUpdateAt.textContent = data.updated_at ?? data.started_at ?? '-';
            }

            function setResetWarningVisible(visible) {
                if (visible) {
                    forceResetWarning.classList.remove('d-none');
                } else {
                    forceResetWarning.classList.add('d-none');
                    forceResetCheckbox.checked = false;
                }
            }

            async function checkUpdate() {
                setButtons(true, false);
                setAlert('info', 'Checking for updates...');

                try {
                    const response = await fetch("{{ route('admin.settings.site_update.check') }}");
                    const data = await response.json();

                    if (!response.ok || !data.ok) {
                        throw new Error(data.message || 'Failed to check update.');
                    }

                    setCommitValues(data);
                    runStatus.textContent = 'idle';
                    isUpdateAvailable = Boolean(data.update_available);
                    resetRequired = Boolean(data.requires_force_reset);
                    setResetWarningVisible(resetRequired);

                    if (data.update_available) {
                        refreshRunButtonState();
                        setAlert(resetRequired ? 'danger' : 'warning', data.message || 'Update available. Click "Run Update".');
                    } else {
                        setButtons(false, false);
                        setResetWarningVisible(false);
                        setAlert('success', 'Already up to date.');
                    }
                } catch (error) {
                    isUpdateAvailable = false;
                    resetRequired = false;
                    setButtons(false, false);
                    setResetWarningVisible(false);
                    setAlert('danger', error.message || 'Update check failed.');
                }
            }

            async function fetchStatus() {
                try {
                    const response = await fetch("{{ route('admin.settings.site_update.status') }}", {
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        if (expectStatusUpdates) {
                            statusErrorCount += 1;
                            setAlert('info', 'Update is still running. Waiting for status...');
                        }
                        return;
                    }

                    const data = await response.json();
                    if (!data.ok) {
                        if (expectStatusUpdates) {
                            statusErrorCount += 1;
                            setAlert('info', 'Update is still running. Waiting for status...');
                        }
                        return;
                    }
                    statusErrorCount = 0;
                    const status = data.status || {};
                    runStatus.textContent = status.state || 'idle';
                    syncState.textContent = status.sync_state || syncState.textContent || '-';
                    requiresReset.textContent = status.requires_force_reset ? 'yes' : (requiresReset.textContent || '-');
                    lastUpdateAt.textContent = status.updated_at || status.started_at || lastUpdateAt.textContent ||
                        '-';

                    if (status.local_commit) {
                        localCommit.textContent = status.local_commit;
                    }

                    if (status.remote_commit) {
                        remoteCommit.textContent = status.remote_commit;
                    }

                    if (status.state === 'running') {
                        expectStatusUpdates = true;
                        setButtons(true, false);
                        setAlert('info', status.message || 'Update is running...');
                        startPolling();
                    } else if (status.state === 'completed') {
                        expectStatusUpdates = false;
                        isUpdateAvailable = false;
                        resetRequired = false;
                        setButtons(false, false);
                        setResetWarningVisible(false);
                        setAlert('success', status.message || 'Update completed successfully.');
                        stopPolling();
                    } else if (status.state === 'failed') {
                        expectStatusUpdates = false;
                        isUpdateAvailable = true;
                        resetRequired = Boolean(status.requires_force_reset);
                        setResetWarningVisible(resetRequired);
                        refreshRunButtonState();
                        setAlert('danger', status.message || 'Update failed.');
                        stopPolling();
                    }
                } catch (error) {
                    if (expectStatusUpdates) {
                        statusErrorCount += 1;
                        if (statusErrorCount <= 5) {
                            setAlert('info', 'Update is running. Trying to reconnect...');
                        } else {
                            setAlert('warning', 'Status check delayed during maintenance. Auto-check continues...');
                        }
                        return;
                    }

                    stopPolling();
                }
            }

            function startPolling() {
                if (pollTimer) {
                    return;
                }

                pollTimer = setInterval(fetchStatus, 4000);
            }

            function stopPolling() {
                if (!pollTimer) {
                    return;
                }

                clearInterval(pollTimer);
                pollTimer = null;
            }

            async function runUpdate() {
                runButton.disabled = true;
                setAlert('info', 'Starting update...');

                try {
                    const response = await fetch("{{ route('admin.settings.site_update.run') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            force_reset: forceResetCheckbox.checked ? 1 : 0,
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok || !data.ok) {
                        throw new Error(data.message || 'Failed to start updater.');
                    }

                    runStatus.textContent = 'running';
                    expectStatusUpdates = true;
                    statusErrorCount = 0;
                    setButtons(true, false);
                    setAlert('info', data.message || 'Update started.');
                    await fetchStatus();
                    startPolling();
                } catch (error) {
                    expectStatusUpdates = false;
                    refreshRunButtonState();
                    setAlert('danger', error.message || 'Could not start updater.');
                }
            }

            checkButton.addEventListener('click', checkUpdate);
            runButton.addEventListener('click', runUpdate);
            forceResetCheckbox.addEventListener('change', refreshRunButtonState);

            fetchStatus();
        })();
    </script>
@endsection
