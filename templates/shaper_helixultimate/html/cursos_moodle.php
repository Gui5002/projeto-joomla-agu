<div class="container" style="margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); font-family: inherit;">
    <h2 style="margin-bottom: 20px; font-weight: bold; color: #333;">Catálogo de Cursos</h2>

    <?php
    $domainname = 'http://localhost:8080/moodle'; 
    $token = 'dd99436b7e644997519ebe32fa858fc9';            

    // 1. Busca as Categorias do Moodle
    $catUrl = $domainname . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=core_course_get_categories&moodlewsrestformat=json';
    $chCat = curl_init($catUrl);
    curl_setopt($chCat, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chCat, CURLOPT_TIMEOUT, 10);
    $catResponse = curl_exec($chCat);
    curl_close($chCat);
    $categoriesRaw = json_decode($catResponse, true);

    $categoryMap = [];
    if (!empty($categoriesRaw) && is_array($categoriesRaw) && !isset($categoriesRaw['errorcode'])) {
        foreach ($categoriesRaw as $cat) {
            if (isset($cat['id']) && isset($cat['name'])) {
                $categoryMap[$cat['id']] = $cat['name'];
            }
        }
    }

    // 2. Busca os cursos na API do Moodle
    $courseUrl = $domainname . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=core_course_search_courses&criterianame=search&criteriavalue=&moodlewsrestformat=json';
    $chCourse = curl_init($courseUrl);
    curl_setopt($chCourse, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chCourse, CURLOPT_TIMEOUT, 10);
    $courseResponse = curl_exec($chCourse);
    curl_close($chCourse);
    $resultData = json_decode($courseResponse, true);

    $coursesRaw = [];
    if (isset($resultData['courses']) && is_array($resultData['courses'])) {
        $coursesRaw = $resultData['courses'];
    } elseif (is_array($resultData)) {
        $coursesRaw = $resultData;
    }

    // 3. Prepara os dados para o JavaScript
    $jsCourses = [];
    if (!empty($coursesRaw) && !isset($coursesRaw['errorcode'])) {
        foreach ($coursesRaw as $course) {
            if (isset($course['id']) && $course['id'] == 1) {
                continue; 
            }

            // Captura rigorosa da imagem de capa (overviewfiles)
            $courseImage = '';
            if (!empty($course['overviewfiles']) && is_array($course['overviewfiles'])) {
                foreach ($course['overviewfiles'] as $file) {
                    if (!empty($file['fileurl'])) {
                        // Garante que o token seja injetado corretamente na URL do arquivo do Moodle
                        $separator = (strpos($file['fileurl'], '?') === false) ? '?' : '&';
                        $courseImage = $file['fileurl'] . $separator . 'token=' . $token;
                        break;
                    }
                }
            }

            $catId = isset($course['categoryid']) ? intval($course['categoryid']) : 0;
            $catName = isset($categoryMap[$catId]) ? $categoryMap[$catId] : 'Geral';
            $summary = isset($course['summary']) ? strip_tags($course['summary']) : 'Acesse para ver os detalhes do treinamento.';
            
            $jsCourses[] = [
                'id'       => isset($course['id']) ? $course['id'] : 0,
                'fullname' => isset($course['fullname']) ? $course['fullname'] : '',
                'summary'  => mb_substr($summary, 0, 80) . '...',
                'catid'    => $catId,
                'catname'  => $catName,
                'image'    => $courseImage,
                'link'     => $domainname . '/course/view.php?id=' . (isset($course['id']) ? $course['id'] : 0)
            ];
        }
    }
    ?>

    <style>
        .moodle-filter-bar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 25px; background: #f8f9fa; padding: 15px; border-radius: 6px; }
        .moodle-search-input { flex: 1; min-width: 250px; padding: 10px 15px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .moodle-select-category { padding: 10px 15px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; background: #fff; min-width: 200px; cursor: pointer; }
        .moodle-pagination { display: flex; justify-content: center; gap: 5px; margin-top: 30px; align-items: center; flex-wrap: wrap; }
        .moodle-pagination button { padding: 8px 14px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #0066cc; font-size: 14px; background: #fff; cursor: pointer; }
        .moodle-pagination button.active { background: #0066cc; color: #fff; border-color: #0066cc; }
        .moodle-pagination button:hover:not(.active) { background: #f1f3f5; }
        .course-card { border: 1px solid #e1e1e1; border-radius: 6px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.04); display: flex; flex-direction: column; overflow: hidden; }
    </style>

    <div class="moodle-filter-bar">
        <input type="text" id="liveSearchInput" class="moodle-search-input" placeholder="Digite para buscar cursos instantaneamente...">

        <select id="liveCategorySelect" class="moodle-select-category">
            <option value="0">Todas as Categorias</option>
            <?php 
            foreach ($categoryMap as $catId => $catName) {
                echo '<option value="' . $catId . '">' . htmlspecialchars($catName) . '</option>';
            }
            ?>
        </select>
    </div>

    <div id="moodleCoursesGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;"></div>
    <div id="moodlePagination" class="moodle-pagination"></div>

    <script>
        const allCourses = <?php echo json_encode($jsCourses); ?>;
        const perPage = 8;
        let currentPage = 1;

        const searchInput = document.getElementById('liveSearchInput');
        const categorySelect = document.getElementById('liveCategorySelect');
        const gridContainer = document.getElementById('moodleCoursesGrid');
        const paginationContainer = document.getElementById('moodlePagination');

        function renderCatalog() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedCat = parseInt(categorySelect.value) || 0;

            const filtered = allCourses.filter(course => {
                const matchesSearch = course.fullname.toLowerCase().includes(searchTerm) || course.summary.toLowerCase().includes(searchTerm);
                const matchesCat = (selectedCat === 0 || course.catid === selectedCat);
                return matchesSearch && matchesCat;
            });

            const totalPages = Math.ceil(filtered.length / perPage) || 1;
            if (currentPage > totalPages) currentPage = 1;

            const start = (currentPage - 1) * perPage;
            const paginatedCourses = filtered.slice(start, start + perPage);

            gridContainer.innerHTML = '';
            if (paginatedCourses.length === 0) {
                gridContainer.innerHTML = '<p style="color: #666; font-style: italic; text-align: center; padding: 20px; grid-column: 1 / -1;">Nenhum curso encontrado.</p>';
                paginationContainer.innerHTML = '';
                return;
            }

            paginatedCourses.forEach(course => {
                // Se o curso tiver imagem, exibe o banner; se falhar ou não tiver, exibe um banner estilizado padrão
                let bannerHtml = course.image 
                    ? `<div style="height: 130px; background-image: url('${course.image}'); background-size: cover; background-position: center; background-color: #f1f3f5;"></div>`
                    : `<div style="height: 130px; background: linear-gradient(135deg, #0066cc 0%, #004080 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 14px; font-weight: bold; letter-spacing: 1px;">ESAGU CURSOS</div>`;

                const card = document.createElement('div');
                card.className = 'course-card';
                card.innerHTML = `
                    ${bannerHtml}
                    <div style="padding: 15px; display: flex; flex-direction: column; flex: 1; justify-content: space-between;">
                        <div>
                            <h3 style="margin-top: 0; color: #0066cc; font-size: 16px; margin-bottom: 8px;">${escapeHtml(course.fullname)}</h3>
                            <p style="color: #666; font-size: 13px; line-height: 1.4; margin-bottom: 12px;">${escapeHtml(course.summary)}</p>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #495057; background: #f1f3f5; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 12px; font-weight: 500;">📁 ${escapeHtml(course.catname)}</div>
                            <br>
                            <a href="${course.link}" target="_blank" style="display: block; text-align: center; background: #0066cc; color: #fff; padding: 8px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: 500;">Acessar Curso</a>
                        </div>
                    </div>
                `;
                gridContainer.appendChild(card);
            });

            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            paginationContainer.innerHTML = '';
            if (totalPages <= 1) return;

            if (currentPage > 1) {
                const prevBtn = document.createElement('button');
                prevBtn.textContent = '« Anterior';
                prevBtn.onclick = () => { currentPage--; renderCatalog(); };
                paginationContainer.appendChild(prevBtn);
            }

            const range = 2;
            let showDotsStart = false;
            let showDotsEnd = false;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - range && i <= currentPage + range)) {
                    const pageBtn = document.createElement('button');
                    pageBtn.textContent = i;
                    if (i === currentPage) pageBtn.className = 'active';
                    pageBtn.onclick = () => { currentPage = i; renderCatalog(); };
                    paginationContainer.appendChild(pageBtn);
                } else {
                    if (i < currentPage && !showDotsStart && currentPage - range > 2) {
                        const dots = document.createElement('span');
                        dots.textContent = '...';
                        dots.style.padding = '8px 6px';
                        paginationContainer.appendChild(dots);
                        showDotsStart = true;
                    }
                    if (i > currentPage && !showDotsEnd && currentPage + range < totalPages - 1) {
                        const dots = document.createElement('span');
                        dots.textContent = '...';
                        dots.style.padding = '8px 6px';
                        paginationContainer.appendChild(dots);
                        showDotsEnd = true;
                    }
                }
            }

            if (currentPage < totalPages) {
                const nextBtn = document.createElement('button');
                nextBtn.textContent = 'Próximo »';
                nextBtn.onclick = () => { currentPage++; renderCatalog(); };
                paginationContainer.appendChild(nextBtn);
            }
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        searchInput.addEventListener('input', () => { currentPage = 1; renderCatalog(); });
        categorySelect.addEventListener('change', () => { currentPage = 1; renderCatalog(); });

        renderCatalog();
    </script>
</div>