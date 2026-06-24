import { createRoot } from 'react-dom/client';
import { useState } from 'react';

function App() {
    const [count, setCount] = useState(0);

    return (
        <div className="p-6 text-center">
            <h1 className="text-2xl font-bold mb-4">React + Laravel</h1>
            <button
                onClick={() => setCount((c) => c + 1)}
                className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
                Count is {count}
            </button>
        </div>
    );
}

const root = createRoot(document.getElementById('app'));
root.render(<App />);
