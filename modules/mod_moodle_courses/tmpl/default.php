<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/** @var array $courses */
/** @var string $moodleUrl */
/** @var bool $showSummary */

$categoriesList = [];
$yearsList      = [];

foreach ($courses as $c) {
    if (!empty($c['category_name']) && $c['category_name'] !== 'Cursos') {
        $categoriesList[$c['category_name']] = $c['category_name'];
    }
    if (!empty($c['startdate'])) {
        $year = date('Y', (int)$c['startdate']);
        $yearsList[$year] = $year;
    }
}

ksort($categoriesList);
rsort($yearsList);
?>

<style>
.esagu-moodle-wrapper {
    width: 100%;
    max-width: 1280px;
    margin: 3.5rem auto 0 auto !important;
    padding: 0 1.25rem;
    box-sizing: border-box;
}

/* 1. Barra de Busca */
.esagu-search-container {
    max-width: 960px;
    margin: 0 auto 1.25rem auto;
}

.esagu-search-pill-box {
    position: relative;
    width: 100%;
    background: #ffffff;
    border: 2.5px solid #1a314c;
    border-radius: 50px !important;
    padding: 4px 6px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    transition: all 0.25s ease;
    box-sizing: border-box;
}

.esagu-search-pill-box:focus-within {
    border-color: #1a314c;
    box-shadow: 0 0 0 4px rgba(26, 49, 76, 0.12);
}

.esagu-search-pill-box input {
    width: 100% !important;
    height: 46px !important;
    border: none !important;
    outline: none !important;
    background: transparent !important;
    padding: 0 16px 0 20px !important;
    font-size: 1rem !important;
    color: #334155 !important;
    box-shadow: none !important;
}

.esagu-search-pill-box .search-icon-btn {
    background: transparent;
    border: none;
    padding: 0 16px 0 8px;
    color: #1a314c;
    cursor: pointer;
    display: flex;
    align-items: center;
}

/* 2. Filtros em Pílula (#1a314c) */
.esagu-filter-pill-row {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 10px !important;
    margin-bottom: 2.25rem !important;
}

.esagu-workload-range-group {
    display: flex;
    align-items: center;
    gap: 6px;
}

.esagu-workload-range-group span {
    font-size: 0.85rem;
    color: #1a314c;
    font-weight: 500;
}

.esagu-pill-item {
    width: auto !important;
    max-width: 100%;
    min-width: 105px;
    height: 38px !important;
    padding: 0 26px 0 14px !important;
    border-radius: 50px !important;
    border: 1.5px solid #1a314c !important;
    background-color: #ffffff !important;
    color: #1a314c !important;
    font-size: 0.88rem !important;
    font-weight: 500 !important;
    cursor: pointer;
    outline: none !important;
    box-shadow: none !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%231a314c' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 10px center !important;
    transition: all 0.2s ease !important;
}

#filter-sort {
    min-width: 125px !important;
    padding-right: 26px !important;
    background-position: right 10px center !important;
}

.esagu-pill-clear {
    height: 38px !important;
    padding: 0 18px !important;
    border-radius: 50px !important;
    border: 1.5px solid #dbe2ea !important;
    background-color: #f1f5f9 !important;
    color: #1a314c !important;
    font-size: 0.88rem !important;
    font-weight: 500 !important;
    cursor: pointer;
    outline: none !important;
    transition: all 0.2s ease !important;
}

/* 3. Cards */
.esagu-moodle-cards .course-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #eef2f6;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
    transition: transform 0.28s ease, box-shadow 0.28s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 14px;
    box-sizing: border-box;
}

.esagu-moodle-cards .course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 28px rgba(0, 43, 91, 0.10);
}

.esagu-moodle-cards .card-thumb-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background-color: #0b1f3a;
    border-radius: 12px;
    overflow: hidden;
    display: block;
    text-decoration: none;
}

.esagu-moodle-cards .card-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.35s ease;
}

.esagu-moodle-cards .course-card:hover .card-thumb-img {
    transform: scale(1.04);
}

.esagu-moodle-cards .banner-card-eva {
    width: 100%;
    height: 100%;
    padding: 1.2rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: radial-gradient(circle at 85% 15%, #173d6d 0%, #081a33 100%);
    box-sizing: border-box;
}

.esagu-moodle-cards .banner-top-tag {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #38bdf8;
}

.esagu-moodle-cards .banner-course-title {
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.35;
    color: #ffffff;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.esagu-moodle-cards .banner-footer-logo {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.68rem;
    color: #94a3b8;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 5px;
}

.esagu-moodle-cards .course-body {
    padding: 1rem 0.35rem 0.35rem 0.35rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.esagu-moodle-cards .course-title {
    font-size: 1.05rem;
    font-weight: 700;
    line-height: 1.35;
    margin-bottom: 0.6rem;
}

.esagu-moodle-cards .course-title a {
    color: #0f172a;
    text-decoration: none;
}

.esagu-moodle-cards .course-summary {
    font-size: 0.85rem;
    color: #64748b;
    line-height: 1.45;
    margin-bottom: 0.75rem;
}

.esagu-moodle-cards .course-meta {
    font-size: 0.8rem;
    color: #8c9ba5;
    margin-top: auto;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.esagu-moodle-cards .badge-workload {
    background-color: #f0fdf4;
    color: #166534;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
}

/* 4. Paginação (#1a314c) */
.esagu-pagination {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 6px !important;
    margin-top: 2.5rem !important;
    margin-bottom: 2rem !important;
    flex-wrap: wrap !important;
}

.esagu-page-btn {
    width: 36px !important;
    height: 36px !important;
    padding: 0 !important;
    border-radius: 50% !important;
    border: 1.5px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 0.88rem !important;
    cursor: pointer;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.esagu-page-btn.active {
    background: #1a314c !important;
    border-color: #1a314c !important;
    color: #ffffff !important;
}

.esagu-page-dots {
    color: #94a3b8;
    padding: 0 4px;
    font-weight: bold;
}
</style>

<div class="esagu-moodle-wrapper" id="esagu-moodle-app">
    <!-- Barra de Busca -->
    <div class="esagu-search-container">
        <div class="esagu-search-pill-box">
            <input type="text" id="esagu-search-input" placeholder="Buscar por nome do curso..." autocomplete="off">
            <span class="search-icon-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
        </div>
    </div>

    <!-- Filtros em Pílula -->
    <div class="esagu-filter-pill-row">
        <!-- Ordenação dos Cursos -->
        <select id="filter-sort" class="esagu-pill-item">
            <option value="az">Ordem: A - Z</option>
            <option value="za">Ordem: Z - A</option>
            <option value="newest">Mais Recentes</option>
            <option value="oldest">Mais Antigos</option>
        </select>

        <!-- Intervalo de Carga Horária (Mín - Máx) -->
        <div class="esagu-workload-range-group">
            <select id="filter-workload-min" class="esagu-pill-item">
                <option value="">Carga Mín.</option>
                <option value="1">1h</option>
                <option value="2">2h</option>
                <option value="4">4h</option>
                <option value="8">8h</option>
                <option value="12">12h</option>
                <option value="20">20h</option>
                <option value="30">30h</option>
                <option value="40">40h</option>
                <option value="60">60h</option>
            </select>

            <span>até</span>

            <select id="filter-workload-max" class="esagu-pill-item">
                <option value="">Carga Máx.</option>
                <option value="2">2h</option>
                <option value="4">4h</option>
                <option value="8">8h</option>
                <option value="12">12h</option>
                <option value="20">20h</option>
                <option value="30">30h</option>
                <option value="40">40h</option>
                <option value="60">60h</option>
                <option value="100">100h+</option>
            </select>
        </div>

        <select id="filter-category" class="esagu-pill-item">
            <option value="">Categorias</option>
            <?php foreach ($categoriesList as $cat) : ?>
                <option value="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>

        <select id="filter-year" class="esagu-pill-item">
            <option value="">Data</option>
            <?php foreach ($yearsList as $yr) : ?>
                <option value="<?php echo htmlspecialchars($yr, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($yr, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>

        <button type="button" id="filter-clear-btn" class="esagu-pill-clear">Limpar Filtro</button>
    </div>

    <!-- Grid de Cursos -->
    <div class="mod-moodle-courses esagu-moodle-cards">
        <div id="no-courses-alert" class="alert alert-info border-0 shadow-sm rounded-4 text-center d-none">
            Nenhum curso encontrado para os critérios informados.
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="courses-grid">
            <?php foreach ($courses as $course) : ?>
                <?php
                    $courseId      = (int) $course['id'];
                    $fullname      = htmlspecialchars($course['fullname'] ?? '', ENT_QUOTES, 'UTF-8');
                    $rawSummary    = $course['summary'] ?? '';
                    $cleanSummary  = trim(preg_replace('/\s+/', ' ', strip_tags($rawSummary)));
                    $shortDesc     = HTMLHelper::_('string.truncate', $cleanSummary, 90, true, false);
                    $courseUrl     = 'https://eva.agu.gov.br/course/view.php?id=' . $courseId;
                    $imageUrl      = $course['image_url'] ?? '';

                    $startDate     = !empty($course['startdate']) ? (int) $course['startdate'] : 0;
                    $formattedDate = $startDate ? HTMLHelper::_('date', $startDate, 'd F Y') : '28 Julho 2026';
                    $startYear     = $startDate ? date('Y', $startDate) : '';
                    $categoryName  = htmlspecialchars($course['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
                    $workload      = htmlspecialchars($course['workload'] ?? '', ENT_QUOTES, 'UTF-8');
                    $workloadVal   = (float) filter_var($workload, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                    $cleanName     = preg_replace(
                        ['/[áàãâä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòõôö]/u', '/[úùûü]/u', '/[ç]/u'],
                        ['a', 'e', 'i', 'o', 'u', 'c'],
                        mb_strtolower($fullname, 'UTF-8')
                    );
                ?>
                <div class="col course-item"
                     data-id="<?php echo $courseId; ?>"
                     data-title="<?php echo strtolower($fullname); ?>"
                     data-cleantitle="<?php echo htmlspecialchars($cleanName, ENT_QUOTES, 'UTF-8'); ?>"
                     data-summary="<?php echo strtolower($cleanSummary); ?>"
                     data-category="<?php echo $categoryName; ?>"
                     data-workload="<?php echo $workloadVal; ?>"
                     data-year="<?php echo $startYear; ?>"
                     data-date="<?php echo $startDate; ?>">
                    <div class="course-card">
                        <a href="<?php echo htmlspecialchars($courseUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="card-thumb-wrapper">
                            <?php if (!empty($imageUrl)) : ?>
                                <img src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo $fullname; ?>"
                                     class="card-thumb-img"
                                     loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                <div class="banner-card-eva d-none">
                                    <span class="banner-top-tag">Curso EVA</span>
                                    <h6 class="banner-course-title"><?php echo $fullname; ?></h6>
                                    <div class="banner-footer-logo">
                                        <span>Escola Superior</span>
                                        <span class="logo-brand">AGU</span>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="banner-card-eva">
                                    <span class="banner-top-tag">Curso EVA</span>
                                    <h6 class="banner-course-title"><?php echo $fullname; ?></h6>
                                    <div class="banner-footer-logo">
                                        <span>Escola Superior</span>
                                        <span class="logo-brand">AGU</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="course-body">
                            <h5 class="course-title">
                                <a href="<?php echo htmlspecialchars($courseUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo $fullname; ?>
                                </a>
                            </h5>

                            <?php if ($showSummary && !empty($shortDesc)) : ?>
                                <p class="course-summary">
                                    <?php echo htmlspecialchars($shortDesc, ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            <?php endif; ?>

                            <div class="course-meta">
                                <span><?php echo $formattedDate; ?></span>
                                <?php if (!empty($workload)) : ?>
                                    <span class="badge-workload"><?php echo $workload; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="esagu-pagination" id="esagu-pagination"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemsPerPage = 21;
    let currentPage = 1;
    
    const coursesGrid = document.getElementById('courses-grid');
    const items = Array.from(document.querySelectorAll('.course-item'));
    const searchInput = document.getElementById('esagu-search-input');
    const filterSort = document.getElementById('filter-sort');
    const filterCat = document.getElementById('filter-category');
    const filterWlMin = document.getElementById('filter-workload-min');
    const filterWlMax = document.getElementById('filter-workload-max');
    const filterYr = document.getElementById('filter-year');
    const clearBtn = document.getElementById('filter-clear-btn');
    const paginationContainer = document.getElementById('esagu-pagination');
    const noCoursesAlert = document.getElementById('no-courses-alert');
    const appWrapper = document.getElementById('esagu-moodle-app');

    function applyFilter() {
        const query = searchInput.value.trim().toLowerCase();
        const sortType = filterSort.value;
        const cat = filterCat.value;
        const wlMin = filterWlMin.value !== '' ? parseFloat(filterWlMin.value) : null;
        const wlMax = filterWlMax.value !== '' ? parseFloat(filterWlMax.value) : null;
        const yr = filterYr.value;

        let visibleItems = items.filter(item => {
            const title = item.dataset.title || '';
            const summary = item.dataset.summary || '';
            const itemCat = item.dataset.category || '';
            const itemWl = item.dataset.workload !== '' ? parseFloat(item.dataset.workload) : null;
            const itemYr = item.dataset.year || '';

            const matchQuery = !query || title.includes(query) || summary.includes(query);
            const matchCat = !cat || itemCat === cat;
            const matchYr = !yr || itemYr === yr;

            let matchWl = true;
            if (wlMin !== null || wlMax !== null) {
                if (itemWl === null || isNaN(itemWl)) {
                    matchWl = false;
                } else {
                    if (wlMin !== null && itemWl < wlMin) matchWl = false;
                    if (wlMax !== null && itemWl > wlMax) matchWl = false;
                }
            }

            return matchQuery && matchCat && matchWl && matchYr;
        });

        visibleItems.sort((a, b) => {
            const titleA = a.dataset.cleantitle || '';
            const titleB = b.dataset.cleantitle || '';
            const dateA = parseInt(a.dataset.date || 0, 10);
            const dateB = parseInt(b.dataset.date || 0, 10);
            const idA = parseInt(a.dataset.id || 0, 10);
            const idB = parseInt(b.dataset.id || 0, 10);

            if (sortType === 'az') {
                return titleA.localeCompare(titleB, 'pt-BR', { numeric: true });
            } else if (sortType === 'za') {
                return titleB.localeCompare(titleA, 'pt-BR', { numeric: true });
            } else if (sortType === 'newest') {
                return dateB !== dateA ? dateB - dateA : idB - idA;
            } else if (sortType === 'oldest') {
                return dateA !== dateB ? dateA - dateB : idA - idB;
            }
            return 0;
        });

        visibleItems.forEach(el => coursesGrid.appendChild(el));

        items.forEach(el => el.style.display = 'none');

        if (visibleItems.length === 0) {
            noCoursesAlert.classList.remove('d-none');
            paginationContainer.innerHTML = '';
            return;
        } else {
            noCoursesAlert.classList.add('d-none');
        }

        const totalPages = Math.ceil(visibleItems.length / itemsPerPage);
        if (currentPage > totalPages) currentPage = 1;

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        visibleItems.slice(start, end).forEach(el => el.style.display = '');

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        paginationContainer.innerHTML = '';
        if (totalPages <= 1) return;

        const prevBtn = document.createElement('button');
        prevBtn.className = 'esagu-page-btn';
        prevBtn.innerHTML = '&lsaquo;';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                applyFilter();
                appWrapper.scrollIntoView({ behavior: 'smooth' });
            }
        });
        paginationContainer.appendChild(prevBtn);

        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage < maxVisible - 1) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        if (startPage > 1) {
            addPageBtn(1);
            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.className = 'esagu-page-dots';
                dots.textContent = '...';
                paginationContainer.appendChild(dots);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            addPageBtn(i);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'esagu-page-dots';
                dots.textContent = '...';
                paginationContainer.appendChild(dots);
            }
            addPageBtn(totalPages);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'esagu-page-btn';
        nextBtn.innerHTML = '&rsaquo;';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                applyFilter();
                appWrapper.scrollIntoView({ behavior: 'smooth' });
            }
        });
        paginationContainer.appendChild(nextBtn);
    }

    function addPageBtn(pageNumber) {
        const pageBtn = document.createElement('button');
        pageBtn.className = 'esagu-page-btn' + (pageNumber === currentPage ? ' active' : '');
        pageBtn.textContent = pageNumber;
        pageBtn.addEventListener('click', () => {
            currentPage = pageNumber;
            applyFilter();
            appWrapper.scrollIntoView({ behavior: 'smooth' });
        });
        paginationContainer.appendChild(pageBtn);
    }

    searchInput.addEventListener('input', () => { currentPage = 1; applyFilter(); });
    filterSort.addEventListener('change', () => { currentPage = 1; applyFilter(); });
    filterCat.addEventListener('change', () => { currentPage = 1; applyFilter(); });
    filterWlMin.addEventListener('change', () => { currentPage = 1; applyFilter(); });
    filterWlMax.addEventListener('change', () => { currentPage = 1; applyFilter(); });
    filterYr.addEventListener('change', () => { currentPage = 1; applyFilter(); });

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        filterSort.value = 'az';
        filterCat.value = '';
        filterWlMin.value = '';
        filterWlMax.value = '';
        filterYr.value = '';
        currentPage = 1;
        applyFilter();
    });

    applyFilter();
});
</script>