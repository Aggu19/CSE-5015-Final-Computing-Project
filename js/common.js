function toggleChatBox(){
    const chatBox = document.getElementById('chatBox');
    if (chatBox.style.display === 'none' || chatBox.style.display === '') {
        chatBox.style.display = 'block';
    } else {
        chatBox.style.display = 'none';
    }
}
function usernotloggedin(){
    alert("Please log in to book this service");
} 