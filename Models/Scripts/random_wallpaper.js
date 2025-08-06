async function setRandomBackground() {
    try {
        const response = await fetch('Processes/random_wallpaper.php');
        const data = await response.json();
        
        if (data.image) {
            document.body.style.backgroundImage = `url(${data.image})`;
        } else {
            console.error(data.error);
        }
    } catch (error) {
        console.error('Error fetching random background:', error);
    }
}
window.onload = setRandomBackground;
setInterval(setRandomBackground, 1800000);
