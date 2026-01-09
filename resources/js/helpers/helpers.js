// ISO Document Handling
export function formatStatusText(status){
        const text = status.replace(/_/g, ' ');
        return text.charAt(0).toUpperCase() + text.slice(1);
    }

export function getStatusColor(status){
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'submitted_to_idc': 'bg-blue-100 text-blue-800',
            'with_qmr': 'bg-purple-100 text-purple-800',
            'approved': 'bg-green-100 text-green-800',
            'on_hold': 'bg-red-100 text-red-800',
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }