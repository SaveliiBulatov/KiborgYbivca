import { SpriteAnimator, useKeyboardControls } from "@react-three/drei";
import { BallCollider, RapierRigidBody, RigidBody, vec3 } from "@react-three/rapier";
import { useEffect, useRef, useState } from "react";
import { Vector3 } from "three";
import HealthBar from "./HealthBar";
import { useFrame } from "@react-three/fiber";
import { TPlayer } from "../../modules/Server/types";

export type TPlayerProps = {
    onFire?(position: Vector3, team: number | null): void;
    onMovement?(position: Vector3): void;
    getPosVel?(position: Vector3, velocity: Vector3): void;
    getMyPlayer?(player: TPlayer): void;
} & TPlayer;

const Player = ({
    x,
    y,
    vx,
    vy,
    dx,
    dy,
    teamId,
    token,
    hp,
    onFire,
    onMovement,
    // getPosVel,
    // getMyPlayer,
}: TPlayerProps) => {

    const ref = useRef<RapierRigidBody>(null!);

    const shoting = useRef<boolean>(false);

    const [controlKeys, getKeys] = useKeyboardControls();

    const [state, setState] = useState<TPlayer>({
        x,
        y,
        vx,
        vy,
        dx,
        dy,
        hp,
        token,
        teamId,
    });

    const movementController = (up?: boolean, down?: boolean, left?: boolean, right?: boolean) => {

        if (ref.current) {
            const speed = 4;
            const velocity = new Vector3(x, y);

            if (left) { velocity.x -= 1; }
            if (right) { velocity.x += 1; }
            if (up) { velocity.y += 1; }
            if (down) { velocity.y -= 1; }

            velocity.setLength(speed);

            ref.current.setLinvel(velocity, true);

            setState({
                ...state,
                x: vec3(ref.current?.translation()).x,
                y: vec3(ref.current?.translation()).y,
                vx: vec3(ref.current?.linvel()).x,
                vy: vec3(ref.current?.linvel()).y,
            });
            // if (getPosVel && isControlled) {
            //     getPosVel(ref.current.translation() as Vector3, ref.current.linvel() as Vector3);
            // }
        }
    }

    useEffect(() => {
        const mouseDownHandler = (e: MouseEvent) => {
            if (e.button === 0) {
                shoting.current = true;
                console.log("shooting")
            }
        }
        const mouseUpHandler = (e: MouseEvent) => {
            if (e.button === 0) {
                shoting.current = false;
            }
        }

        document.addEventListener("mousedown", mouseDownHandler);
        document.addEventListener("mouseup", mouseUpHandler);

        return () => {
            document.removeEventListener("mousedown", mouseDownHandler);
            document.removeEventListener("mouseup", mouseUpHandler);
        }
    }, []);

    useFrame(() => {
        const { up, down, left, right, shoot } = getKeys();
        movementController(up, down, left, right);

        const playerPosition = vec3(ref.current?.translation());

        if (onMovement) {
            onMovement(playerPosition);
        }

        if (shoot || shoting) {
            if (onFire) {
                onFire(playerPosition, teamId);
            }
        }

        

    });

    return (
        <group>
            <RigidBody
                ref={ref}
                scale={0.5}
                position={[x, y, 0]}
                colliders="hull"
                friction={1}
                linearDamping={10}
                angularDamping={1}
                lockRotations
            >
                <SpriteAnimator
                    fps={10}
                    startFrame={0}
                    loop={true}
                    autoPlay={true}
                    textureImageURL={'./assets/test/Sprite-0001.png'}
                    textureDataURL={'./assets/test/Sprite-0001.json'}
                    alphaTest={0.01}
                />

                <BallCollider args={[0.5]} restitution={0}
                    onIntersectionEnter={(e) => {
                        // const data: any = e.other.rigidBody?.userData;
                        // if (data.type === "projectile") {
                        //     const damage = data.team === teamId ? data.damage / 2 : data.damage;
                        //     if (hp - damage < 0) {
                        //         setState({ ...state, hp: 0 });
                        //     } else {
                        //         setState({ ...state, hp: hp - damage });
                        //     }
                        // }
                    }} />
                <HealthBar value={hp} color={0xff0000} />
            </RigidBody>
        </group>
    );
}

export default Player;
