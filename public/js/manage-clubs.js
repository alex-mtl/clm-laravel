window.getUsersByClub = function(clubId) {
    fetch('/clubs/' + clubId+'/members',{
        headers: {
            'X-Ajax-Request': 'true'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(data => {
            console.log(data);
            document.getElementById('club-users').innerHTML = data;

        })
        .catch(error => {
            console.log('Error:', error);
        });
};
