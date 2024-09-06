document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('newsModal');
    var newsContent = document.getElementById('newsContent');
    var closeBtn = document.getElementsByClassName('close')[0];

    // Открытие модального окна при клике на "Читать далее"
    document.querySelectorAll('.read-more').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var newsId = this.getAttribute('data-id');
            fetchNewsContent(newsId);
        });
    });

    // Закрытие модального окна при клике на крестик
    closeBtn.onclick = function() {
        closeModal();
    }

    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    // Функция для загрузки содержимого новости
    function fetchNewsContent(id) {
        fetch('get_news.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    newsContent.innerHTML = `
                        <h2>${data.title}</h2>
                        <p class="news-date">${data.date}</p>
                        <div class="news-full-content">${data.content}</div>
                    `;
                    openModal();
                } else {
                    alert('Не удалось загрузить новость');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Функция для открытия модального окна
    function openModal() {
        modal.style.display = "block";
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }

    // Функция для закрытия модального окна
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = "none";
        }, 300);
    }
});
