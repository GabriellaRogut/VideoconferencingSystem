const WebSocket = require("ws");

const wss = new WebSocket.Server({ port: 3000 });

// Store clients per meeting code
const meetings = {};

wss.on("connection", (ws) => {
  let meetingCode = null;

  ws.on("message", (msg) => {
    const data = JSON.parse(msg);

    // First message: join meeting
    if (data.type === "join") {
      meetingCode = data.code;

      if (!meetings[meetingCode]) meetings[meetingCode] = [];
      meetings[meetingCode].push(ws);
      console.log(`User joined meeting ${meetingCode}`);

      // If 2 participants are in the meeting, tell them to start the call
      if (meetings[meetingCode].length === 2) {
        meetings[meetingCode].forEach(client => {
          if (client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify({ type: "start-call" }));
          }
        });
      }

      return;
    }

    // Forward signaling messages to other clients in the same meeting
    if (meetingCode && meetings[meetingCode]) {
      meetings[meetingCode].forEach(client => {
        if (client !== ws && client.readyState === WebSocket.OPEN) {
          client.send(JSON.stringify({ ...data, from: ws._id }));
        }
      });
    }
  });

  ws.on("close", () => {
    if (meetingCode && meetings[meetingCode]) {
      meetings[meetingCode] = meetings[meetingCode].filter(c => c !== ws);
      if (meetings[meetingCode].length === 0) delete meetings[meetingCode];
    }
  });
});

console.log("WebSocket server running on ws://localhost:3000");
