// import Vue           from 'vue'
import handleMessage from './handleMessage.js'
import { NotificationProgrammatic as Notification } from 'buefy'

const notifier = function(message, type) {
    let mess = handleMessage(message)

    if (mess) {
        Notification.open({
            duration: 10000,
            message: mess,
            type: `is-${type}`,
            hasIcon: true,
            position: 'is-bottom-right',
        })
    }
}

export default notifier