import * as React from 'react';
import {useEffect, useState} from 'react';
import {Alert} from '@mui/material';

type Level = 'success' | 'error';
type LogEntry = {
    severity: Level;
    text: string;
};
type LogPanelProps = {
    data: LogEntry[];
};

export const LogPanel = ({data}: LogPanelProps) => {
    const [logs, setLogs] = useState(data);
    useEffect(() => {
        setTimeout(() => {
            console.log('clear logs');
            setLogs([]);
        }, 1000);
    }, []);
    return (
        <>
            {!logs || logs.length === 0 ? (
                <>Nothing here</>
            ) : (
                logs.map((entry, index) => (
                    <Alert key={index} variant="outlined" severity={entry.severity} icon={false}>
                        {entry.text}
                    </Alert>
                ))
            )}
        </>
    );
};

export default LogPanel;
