// main script for the app

document.addEventListener("DOMContentLoaded", () => {
    
    const filterForm = document.getElementById('filterForm');
    const paginationContainer = document.getElementById('pagination'); 
    
    let allFilteredTasks = [];
    let currentPage = 1;
    const itemsPerPage = 5;

    // --- event listeners ---
    
    // handle filter changes
    if (filterForm) {
        filterForm.querySelectorAll('select').forEach(input => {
            input.addEventListener('change', fetchTasks);
        });
    }

    // handle clicks on the page (event delegation)
    // this way we dont need onclick in html
    document.addEventListener('click', (e) => {
        // complete button
        if (e.target.matches('.js-mark-complete')) {
            const id = e.target.dataset.id;
            markComplete(id);
        }
        
        // open image modal
        if (e.target.matches('.js-open-modal')) {
            const src = e.target.dataset.src;
            openModal(src);
        }
        
        // close modal
        if (e.target.matches('#imageModal') || e.target.matches('.close-modal')) {
            closeModal();
        }

        // pagination buttons
        if (e.target.matches('.js-change-page')) {
            const page = parseInt(e.target.dataset.page);
            changePage(page);
        }

        // global confirm for delete actions
        if (e.target.matches('.js-confirm')) {
            const msg = e.target.dataset.confirm || 'Opravdu?';
            if (!confirm(msg)) {
                e.preventDefault(); // stop if user cancels
            }
        }
    });

    // check passwords on registration
    const registerForm = document.querySelector('form[action*="register"]'); 
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            const pass = registerForm.querySelector('input[name="password"]').value;
            const confirm = registerForm.querySelector('input[name="password_confirm"]').value;
            if (pass !== confirm) {
                e.preventDefault();
                alert("Hesla se neshodují!");
            }
        });
    }

    // --- functions ---

    function markComplete(taskId) {
        // call backend to update status
        fetch(`complete.php?id=${taskId}&ajax=1`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    fetchTasks(); 
                    updateStats(data.stats);
                }
            })
            .catch(err => console.error('Error:', err));
    }

    function updateStats(stats) {
        if (!stats) return;
        // update numbers on dashboard
        const statTotal = document.getElementById('stat-total');
        const statPending = document.getElementById('stat-pending');
        const statCompleted = document.getElementById('stat-completed');
        if (statTotal) statTotal.innerText = stats.total;
        if (statPending) statPending.innerText = stats.pending;
        if (statCompleted) statCompleted.innerText = stats.completed;
    }

    // load tasks from api
    function fetchTasks() {
        const subjectId = document.querySelector('select[name="subject_id"]').value;
        const status = document.querySelector('select[name="status"]').value;
        const listContainer = document.querySelector('.task-list');

        // update browser url just for looks
        const url = new URL(window.location);
        url.searchParams.set('page', '1');
        window.history.replaceState({}, '', url);

        if(listContainer) listContainer.style.opacity = '0.5';

        fetch(`api.php?subject_id=${subjectId}&status=${status}`)
            .then(response => response.json())
            .then(data => {
                allFilteredTasks = data;
                currentPage = 1;
                renderPage(); 
                if(listContainer) listContainer.style.opacity = '1';
            })
            .catch(err => console.error('Error:', err));
    }

    function renderPage() {
        const listContainer = document.querySelector('.task-list');
        if (!listContainer) return;
        
        listContainer.innerHTML = ''; 

        if (allFilteredTasks.length === 0) {
            listContainer.innerHTML = '<div class="card text-center text-muted">Seznam je prázdný.</div>';
            if (paginationContainer) paginationContainer.innerHTML = ''; 
            return;
        }

        // calculate pages
        const totalPages = Math.ceil(allFilteredTasks.length / itemsPerPage);
        
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // slice data
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const tasksToShow = allFilteredTasks.slice(startIndex, endIndex);

        // build html
        tasksToShow.forEach(task => {
            listContainer.innerHTML += buildTaskCard(task);
        });

        renderPaginationControls(totalPages);
    }

    function renderPaginationControls(totalPages) {
        if (!paginationContainer) return;

        if (totalPages <= 1) {
            paginationContainer.innerHTML = '<span class="mx-10">Strana 1 z 1</span>';
            return;
        }

        let html = '<div class="text-center mt-20">';
        if (currentPage > 1) {
            html += `<button class="btn btn-pagination js-change-page" data-page="${currentPage - 1}">&laquo; Předchozí</button>`;
        }
        html += `<span style="margin: 0 10px;">Strana ${currentPage} z ${totalPages}</span>`;
        if (currentPage < totalPages) {
            html += `<button class="btn btn-pagination js-change-page" data-page="${currentPage + 1}">Další &raquo;</button>`;
        }
        html += '</div>';
        paginationContainer.innerHTML = html;
    }

    function changePage(newPage) {
        currentPage = newPage;
        renderPage();
        const list = document.querySelector('.task-list');
        if(list) list.scrollIntoView({ behavior: 'smooth' });
    }

    // helper to create task html
    function buildTaskCard(task) {
        let subjectBadge = '';
        if (task.subject_name) {
            subjectBadge = `<span class="task-badge">${task.subject_name}</span>`;
        }

        let imageHtml = '';
        if (task.thumb_url) {
            imageHtml = `
            <img 
                src="${task.thumb_url}" 
                class="task-thumb js-open-modal" 
                alt="Příloha"
                loading="lazy" 
                data-src="${task.full_url}"
            >`;
        }

        let desc = '';
        if (task.description) {
            desc = `<p class="task-desc">${task.description}</p>`;
        }

        let statusLabel = '', statusClass = '', daysText = '', rowClass = '';
        let doneButtonHtml = '';

        if (task.status === 'completed') {
            statusLabel = 'Hotovo';
            statusClass = 'completed';
        } else if (task.is_overdue) {
            statusLabel = 'PO TERMÍNU';
            statusClass = 'overdue';
            rowClass = 'overdue';
            doneButtonHtml = `<button class="btn btn-success btn-sm mr-5 js-mark-complete" data-id="${task.id}">Hotovo</button>`;
        } else {
            statusLabel = 'Nevyřízeno';
            statusClass = 'pending';
            doneButtonHtml = `<button class="btn btn-success btn-sm mr-5 js-mark-complete" data-id="${task.id}">Hotovo</button>`;
        }

        if (task.status !== 'completed') {
            if (task.is_past) {
                daysText = `<span class="text-danger" style="font-weight:bold;">(${task.days_left} dní po termínu)</span>`;
            } else if (task.days_left == 0) {
                daysText = `<span style="color:#e0a800; font-weight:bold;">(Dnes)</span>`;
            } else {
                daysText = `<span class="color-success">(Zbývá ${task.days_left} dní)</span>`;
            }
        }

        const dateDisplay = task.due_date_formatted || task.due_date;
        
        return `
        <div class="task-item ${rowClass}">
            <div class="flex-1">
                ${subjectBadge}
                <strong class="task-title">${task.title}</strong>
                ${desc}
                <small class="text-muted">
                    Termín: ${dateDisplay} ${daysText}
                </small>
                ${imageHtml}
            </div>
            <div class="text-right">
                <span class="status-badge status-${statusClass} mr-10">${statusLabel}</span>
                <br><br>
                ${doneButtonHtml}
                <a href="edit.php?id=${task.id}" class="btn-sm mr-5">Upravit</a>
                <a href="delete.php?type=task&id=${task.id}&token=${document.querySelector('meta[name="csrf-token"]')?.content || ''}" class="text-danger btn-sm js-confirm" data-confirm="Smazat úkol?">Smazat</a>
            </div>
        </div>`;
    }

    function openModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        if(modal && modalImg) {
            modal.style.display = "flex";
            modalImg.src = src;
        }
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        if(modal) modal.style.display = "none";
    }
});